<?php
namespace Aws\Test\S3;

use Aws\Middleware;
use Aws\S3\MultipartUploader;
use Aws\Result;
use Aws\S3\S3Client;
use Aws\Test\UsesServiceTrait;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7;
use Psr\Http\Message\StreamInterface;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * @covers Aws\S3\MultipartUploader
 */
class MultipartUploaderTest extends TestCase
{
    use UsesServiceTrait;

    const MB = 1048576;
    const FILENAME = '_aws-sdk-php-s3-mup-test-dots.txt';

    public static function tear_down_after_class()
    {
        @unlink(sys_get_temp_dir() . '/' . self::FILENAME);
    }

    /**
     * @dataProvider getTestCases
     */
    public function testS3MultipartUploadWorkflow(
        array $clientOptions = [],
        array $uploadOptions = [],
        ?StreamInterface $source = null,
        $error = false
    ) {
        $client = $this->getTestClient('s3', $clientOptions);
        $url = 'http://foo.s3.amazonaws.com/bar';
        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz']),
            new Result(['ETag' => 'A']),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
            new Result(['Location' => $url])
        ]);

        if ($error) {
            if (method_exists($this, 'expectException')) {
                $this->expectException($error);
            } else {
                $this->setExpectedException($error);
            }
        }

        $uploader = new MultipartUploader($client, $source, $uploadOptions);
        $result = $uploader->upload();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function getTestCases()
    {
        $defaults = [
            'bucket' => 'foo',
            'key'    => 'bar',
        ];

        $data = str_repeat('.', 12 * self::MB);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);

        return [
            [ // Seekable stream, regular config
                [],
                ['acl' => 'private'] + $defaults,
                Psr7\Utils::streamFor(fopen($filename, 'r'))
            ],
            [ // Non-seekable stream
                [],
                $defaults,
                Psr7\Utils::streamFor($data)
            ],
            [ // Error: bad part_size
                [],
                ['part_size' => 1] + $defaults,
                Psr7\FnStream::decorate(
                    Psr7\Utils::streamFor($data), [
                        'getSize' => function () {return null;}
                    ]
                ),
                'InvalidArgumentException'
            ],
        ];
    }

    public function testCanLoadStateFromService()
    {
        $client = $this->getTestClient('s3');
        $url = 'http://foo.s3.amazonaws.com/bar';
        $this->addMockResults($client, [
            new Result(['Parts' => [
                ['PartNumber' => 1, 'ETag' => 'A', 'Size' => 4 * self::MB],
            ]]),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
            new Result(['Location' => $url])
        ]);

        $state = MultipartUploader::getStateFromService($client, 'foo', 'bar', 'baz');
        $source = Psr7\Utils::streamFor(str_repeat('.', 9 * self::MB));
        $uploader = new MultipartUploader($client, $source, ['state' => $state]);
        $result = $uploader->upload();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame(4 * self::MB, $uploader->getState()->getPartSize());
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function testCanUseCaseInsensitiveConfigKeys()
    {
        $client = $this->getTestClient('s3');
        $putObjectMup = new MultipartUploader($client, Psr7\Utils::streamFor('x'), [
            'Bucket' => 'bucket',
            'Key' => 'key',
        ]);
        $classicMup = new MultipartUploader($client, Psr7\Utils::streamFor('x'), [
            'bucket' => 'bucket',
            'key' => 'key',
        ]);
        $configProp = (new \ReflectionClass(MultipartUploader::class))
            ->getProperty('config');
        $configProp->setAccessible(true);

        $this->assertSame($configProp->getValue($classicMup), $configProp->getValue($putObjectMup));
    }

    /** @doesNotPerformAssertions */
    public function testMultipartSuccessStreams()
    {
        $size = 12 * self::MB;
        $data = str_repeat('.', $size);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);

        return [
            [ // Seekable stream, regular config
                Psr7\Utils::streamFor(fopen($filename, 'r')),
                $size,
            ],
            [ // Non-seekable stream
                Psr7\Utils::streamFor($data),
                $size,
            ]
        ];
    }

    /**
     * @dataProvider testMultipartSuccessStreams
     */
    public function testS3MultipartUploadParams($stream, $size)
    {
        /** @var \Aws\S3\S3Client $client */
        $client = $this->getTestClient('s3');
        $uploadOptions = [
            'bucket'          => 'foo',
            'key'             => 'bar',
            'params'          => [
                'RequestPayer'  => 'test',
                'ContentLength' => $size
            ],
            'before_initiate' => function($command) {
                $this->assertSame('test', $command['RequestPayer']);
            },
            'before_upload'   => function($command) use ($size) {
                $this->assertLessThan($size, $command['ContentLength']);
                $this->assertSame('test', $command['RequestPayer']);
            },
            'before_complete' => function($command) {
                $this->assertSame('test', $command['RequestPayer']);
            }
        ];
        $url = 'http://foo.s3.amazonaws.com/bar';

        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz']),
            new Result(['ETag' => 'A']),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
            new Result(['Location' => $url])
        ]);

        $uploader = new MultipartUploader($client, $stream, $uploadOptions);
        $result = $uploader->upload();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function getContentTypeSettingTests()
    {
        $size = 12 * self::MB;
        $data = str_repeat('.', $size);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);

        return [
            [ // Successful lookup from filename via stream
                Psr7\Utils::streamFor(fopen($filename, 'r')),
                [],
                'text/plain'
            ],
            [ // Unsuccessful lookup because of no file name
                Psr7\Utils::streamFor($data),
                [],
                'application/octet-stream'
            ],
            [ // Successful override of known type from filename
                Psr7\Utils::streamFor(fopen($filename, 'r')),
                ['ContentType' => 'TestType'],
                'TestType'
            ],
            [ // Successful override of unknown type
                Psr7\Utils::streamFor($data),
                ['ContentType' => 'TestType'],
                'TestType'
            ]
        ];
    }

    /**
     * @dataProvider getContentTypeSettingTests
     */
    public function testS3MultipartContentTypeSetting(
        $stream,
        $params,
        $expectedContentType
    ) {
        /** @var \Aws\S3\S3Client $client */
        $client = $this->getTestClient('s3');
        $uploadOptions = [
            'bucket'          => 'foo',
            'key'             => 'bar',
            'params'          => $params,
            'before_initiate' => function($command) use ($expectedContentType) {
                $this->assertEquals(
                    $expectedContentType,
                    $command['ContentType']
                );
            },
        ];
        $url = 'http://foo.s3.amazonaws.com/bar';

        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz']),
            new Result(['ETag' => 'A']),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
            new Result(['Location' => $url])
        ]);

        $uploader = new MultipartUploader($client, $stream, $uploadOptions);
        $result = $uploader->upload();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function testAppliesAmbiguousSuccessParsing()
    {
        $this->expectExceptionMessage("An exception occurred while uploading parts to a multipart upload");
        $this->expectException(\Aws\S3\Exception\S3MultipartUploadException::class);
        $counter = 0;

        $httpHandler = function ($request, array $options) use (&$counter) {
            if ($counter < 1) {
                $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><OperationNameResponse><UploadId>baz</UploadId></OperationNameResponse>";
            } else {
                $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n\n";
            }
            $counter++;

            return Promise\Create::promiseFor(
                new Psr7\Response(200, [], $body)
            );
        };

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'http_handler' => $httpHandler
        ]);

        $data = str_repeat('.', 12 * 1048576);
        $source = Psr7\Utils::streamFor($data);

        $uploader = new MultipartUploader(
            $s3,
            $source,
            [
                'bucket' => 'test-bucket',
                'key' => 'test-key'
            ]
        );
        $uploader->upload();
    }

    /**
     * @dataProvider testMultipartSuccessStreams
     */
    public function testUploaderAddsFlexibleChecksums($stream, $size)
    {
        /** @var \Aws\S3\S3Client $client */
        $client = $this->getTestClient('s3');
        $uploadOptions = [
            'bucket'          => 'foo',
            'key'             => 'bar',
            'params'          => [
                'RequestPayer'  => 'test',
                'ChecksumAlgorithm' => 'Sha256'
            ],
            'before_initiate' => function($command) {
                $this->assertSame('test', $command['RequestPayer']);
                $this->assertSame('Sha256', $command['ChecksumAlgorithm']);
            },
            'before_upload'   => function($command) use ($size) {
                $this->assertLessThan($size, $command['ContentLength']);
                $this->assertSame('test', $command['RequestPayer']);
                $this->assertSame('Sha256', $command['ChecksumAlgorithm']);
            },
            'before_complete' => function($command) {
                $this->assertSame('test', $command['RequestPayer']);
                $this->assertSame('Sha256', $command['ChecksumAlgorithm']);
            }
        ];
        $url = 'http://foo.s3.amazonaws.com/bar';

        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz', 'ChecksumSHA256' => 'xyz']),
            new Result(['ETag' => 'A', 'ChecksumSHA256' => 'xyz']),
            new Result(['ETag' => 'B', 'ChecksumSHA256' => 'xyz']),
            new Result(['ETag' => 'C', 'ChecksumSHA256' => 'xyz']),
            new Result(['Location' => $url, 'ChecksumSHA256' => 'xyz'])
        ]);

        $uploader = new MultipartUploader($client, $stream, $uploadOptions);
        $result = $uploader->upload();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame('xyz', $result['ChecksumSHA256']);
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function testAddContentMd5EmitsDeprecationNotice()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage('S3 no longer supports MD5 checksums.');
        $data = str_repeat('.', 12 * self::MB);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);
        $source = Psr7\Utils::streamFor(fopen($filename, 'r'));
        $client = $this->getTestClient('s3');
        $options = ['bucket' => 'foo', 'key' => 'bar', 'add_content_md5' => true];
        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz']),
            new Result(['ETag' => 'A']),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
        ]);

        $uploader = new MultipartUploader($client, $source, $options);
        $result = $uploader->upload();
    }
  
    public function testUploadPrintsProgress()
    {
        $progressBar = [
            "Transfer initiated...\n|                    | 0.0%\n",
            "|==                  | 12.5%\n",
            "|=====               | 25.0%\n",
            "|=======             | 37.5%\n",
            "|==========          | 50.0%\n",
            "|============        | 62.5%\n",
            "|===============     | 75.0%\n",
            "|=================   | 87.5%\n",
            "|====================| 100.0%\nTransfer complete!\n"
        ];
        $client = $this->getTestClient('s3');
        $uploadOptions = [
            'bucket'          => 'foo',
            'key'             => 'bar',
            'display_progress' => true
        ];
        $this->expectOutputString(implode("", $progressBar));
        $url = 'http://foo.s3.amazonaws.com/bar';

        $size = 12 * self::MB;
        $data = str_repeat('.', $size);
        $filename = sys_get_temp_dir() . '/' . self::FILENAME;
        file_put_contents($filename, $data);
        $stream = Psr7\Utils::streamFor(fopen($filename, 'r'));

        $this->addMockResults($client, [
            new Result(['UploadId' => 'baz']),
            new Result(['ETag' => 'A']),
            new Result(['ETag' => 'B']),
            new Result(['ETag' => 'C']),
            new Result(['Location' => $url])
        ]);

        $uploader = new MultipartUploader($client, $stream, $uploadOptions);
        $result = $uploader->upload();

        $this->assertTrue($uploader->getState()->isCompleted());
        $this->assertSame($url, $result['ObjectURL']);
    }

    public function testFailedUploadPrintsPartialProgress()
    {
        $partialBar = [
            "Transfer initiated...\n|                    | 0.0%\n",
            "|==                  | 12.5%\n",
            "|=====               | 25.0%\n"
        ];
        $this->expectOutputString(implode("", $partialBar));

        $this->expectExceptionMessage(
            "An exception occurred while uploading parts to a multipart upload"
        );
        $this->expectException(\Aws\S3\Exception\S3MultipartUploadException::class);
        $counter = 0;

        $httpHandler = function ($request, array $options) use (&$counter) {
            if ($counter < 4) {
                $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" .
                     "<OperationNameResponse><UploadId>baz</UploadId></OperationNameResponse>";
            } else {
                $body = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n\n";
            }
            $counter++;

            return Promise\Create::promiseFor(
                new Psr7\Response(200, [], $body)
            );
        };

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'http_handler' => $httpHandler
        ]);

        $data = str_repeat('.', 50 * self::MB);
        $source = Psr7\Utils::streamFor($data);

        $uploader = new MultipartUploader(
            $s3,
            $source,
            [
                'bucket' => 'test-bucket',
                'key' => 'test-key',
                'display_progress' => true
            ]
        );
        $uploader->upload();
    }
}

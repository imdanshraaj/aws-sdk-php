<?php
// This file was auto-generated from sdk-root/src/data/pricing/2017-10-15/docs-2.json
return [ 'version' => '2.0', 'service' => '<p>The Amazon Web Services Price List API is a centralized and convenient way to programmatically query Amazon Web Services for services, products, and pricing information. The Amazon Web Services Price List uses standardized product attributes such as <code>Location</code>, <code>Storage Class</code>, and <code>Operating System</code>, and provides prices at the SKU level. You can use the Amazon Web Services Price List to do the following:</p> <ul> <li> <p>Build cost control and scenario planning tools</p> </li> <li> <p>Reconcile billing data</p> </li> <li> <p>Forecast future spend for budgeting purposes</p> </li> <li> <p>Provide cost benefit analysis that compare your internal workloads with Amazon Web Services</p> </li> </ul> <p>Use <code>GetServices</code> without a service code to retrieve the service codes for all Amazon Web Services services, then <code>GetServices</code> with a service code to retrieve the attribute names for that service. After you have the service code and attribute names, you can use <code>GetAttributeValues</code> to see what values are available for an attribute. With the service code and an attribute name and value, you can use <code>GetProducts</code> to find specific products that you\'re interested in, such as an <code>AmazonEC2</code> instance, with a <code>Provisioned IOPS</code> <code>volumeType</code>.</p> <p>For more information, see <a href="https://docs.aws.amazon.com/awsaccountbilling/latest/aboutv2/price-changes.html">Using the Amazon Web Services Price List API</a> in the <i>Billing User Guide</i>.</p>', 'operations' => [ 'DescribeServices' => '<p>Returns the metadata for one service or a list of the metadata for all services. Use this without a service code to get the service codes for all services. Use it with a service code, such as <code>AmazonEC2</code>, to get information specific to that service, such as the attribute names available for that service. For example, some of the attribute names available for EC2 are <code>volumeType</code>, <code>maxIopsVolume</code>, <code>operation</code>, <code>locationType</code>, and <code>instanceCapacity10xlarge</code>.</p>', 'GetAttributeValues' => '<p>Returns a list of attribute values. Attributes are similar to the details in a Price List API offer file. For a list of available attributes, see <a href="https://docs.aws.amazon.com/awsaccountbilling/latest/aboutv2/reading-an-offer.html#pps-defs">Offer File Definitions</a> in the <a href="https://docs.aws.amazon.com/awsaccountbilling/latest/aboutv2/billing-what-is.html">Billing and Cost Management User Guide</a>.</p>', 'GetPriceListFileUrl' => '<p> <i> <b>This feature is in preview release and is subject to change. Your use of Amazon Web Services Price List API is subject to the Beta Service Participation terms of the <a href="https://aws.amazon.com/service-terms/">Amazon Web Services Service Terms</a> (Section 1.10).</b> </i> </p> <p>This returns the URL that you can retrieve your Price List file from. This URL is based on the <code>PriceListArn</code> and <code>FileFormat</code> that you retrieve from the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_ListPriceLists.html">ListPriceLists</a> response. </p>', 'GetProducts' => '<p>Returns a list of all products that match the filter criteria.</p>', 'ListPriceLists' => '<p> <i> <b>This feature is in preview release and is subject to change. Your use of Amazon Web Services Price List API is subject to the Beta Service Participation terms of the <a href="https://aws.amazon.com/service-terms/">Amazon Web Services Service Terms</a> (Section 1.10).</b> </i> </p> <p>This returns a list of Price List references that the requester if authorized to view, given a <code>ServiceCode</code>, <code>CurrencyCode</code>, and an <code>EffectiveDate</code>. Use without a <code>RegionCode</code> filter to list Price List references from all available Amazon Web Services Regions. Use with a <code>RegionCode</code> filter to get the Price List reference that\'s specific to a specific Amazon Web Services Region. You can use the <code>PriceListArn</code> from the response to get your preferred Price List files through the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_GetPriceListFileUrl.html">GetPriceListFileUrl</a> API.</p>', ], 'shapes' => [ 'AccessDeniedException' => [ 'base' => '<p>General authentication failure. The request wasn\'t signed correctly.</p>', 'refs' => [], ], 'AttributeNameList' => [ 'base' => NULL, 'refs' => [ 'Service$AttributeNames' => '<p>The attributes that are available for this service.</p>', ], ], 'AttributeValue' => [ 'base' => '<p>The values of a given attribute, such as <code>Throughput Optimized HDD</code> or <code>Provisioned IOPS</code> for the <code>Amazon EC2</code> <code>volumeType</code> attribute.</p>', 'refs' => [ 'AttributeValueList$member' => NULL, ], ], 'AttributeValueList' => [ 'base' => NULL, 'refs' => [ 'GetAttributeValuesResponse$AttributeValues' => '<p>The list of values for an attribute. For example, <code>Throughput Optimized HDD</code> and <code>Provisioned IOPS</code> are two available values for the <code>AmazonEC2</code> <code>volumeType</code>.</p>', ], ], 'BoxedInteger' => [ 'base' => NULL, 'refs' => [ 'DescribeServicesRequest$MaxResults' => '<p>The maximum number of results that you want returned in the response.</p>', 'GetAttributeValuesRequest$MaxResults' => '<p>The maximum number of results to return in response.</p>', 'GetProductsRequest$MaxResults' => '<p>The maximum number of results to return in the response.</p>', ], ], 'CurrencyCode' => [ 'base' => NULL, 'refs' => [ 'ListPriceListsRequest$CurrencyCode' => '<p>The three alphabetical character ISO-4217 currency code that the Price List files are denominated in. </p>', 'PriceList$CurrencyCode' => '<p>The three alphabetical character ISO-4217 currency code the Price List files are denominated in. </p>', ], ], 'DescribeServicesRequest' => [ 'base' => NULL, 'refs' => [], ], 'DescribeServicesResponse' => [ 'base' => NULL, 'refs' => [], ], 'EffectiveDate' => [ 'base' => NULL, 'refs' => [ 'ListPriceListsRequest$EffectiveDate' => '<p>The date that the Price List file prices are effective from. </p>', ], ], 'ExpiredNextTokenException' => [ 'base' => '<p>The pagination token expired. Try again without a pagination token.</p>', 'refs' => [], ], 'Field' => [ 'base' => NULL, 'refs' => [ 'Filter$Field' => '<p>The product metadata field that you want to filter on. You can filter by just the service code to see all products for a specific service, filter by just the attribute name to see a specific attribute for multiple services, or use both a service code and an attribute name to retrieve only products that match both fields.</p> <p>Valid values include: <code>ServiceCode</code>, and all attribute names</p> <p>For example, you can filter by the <code>AmazonEC2</code> service code and the <code>volumeType</code> attribute name to get the prices for only Amazon EC2 volumes.</p>', ], ], 'FileFormat' => [ 'base' => NULL, 'refs' => [ 'FileFormats$member' => NULL, 'GetPriceListFileUrlRequest$FileFormat' => '<p>The format that you want to retrieve your Price List files in. The <code>FileFormat</code> can be obtained from the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_ListPriceLists.html">ListPriceLists</a> response. </p>', ], ], 'FileFormats' => [ 'base' => NULL, 'refs' => [ 'PriceList$FileFormats' => '<p>The format you want to retrieve your Price List files. The <code>FileFormat</code> can be obtained from the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_ListPriceLists.html"> <code>ListPriceList</code> </a> response. </p>', ], ], 'Filter' => [ 'base' => '<p>The constraints that you want all returned products to match.</p>', 'refs' => [ 'Filters$member' => NULL, ], ], 'FilterType' => [ 'base' => NULL, 'refs' => [ 'Filter$Type' => '<p>The type of filter that you want to use.</p> <p>Valid values are: <code>TERM_MATCH</code>. <code>TERM_MATCH</code> returns only products that match both the given filter field and the given value.</p>', ], ], 'Filters' => [ 'base' => NULL, 'refs' => [ 'GetProductsRequest$Filters' => '<p>The list of filters that limit the returned products. only products that match all filters are returned.</p>', ], ], 'FormatVersion' => [ 'base' => NULL, 'refs' => [ 'DescribeServicesRequest$FormatVersion' => '<p>The format version that you want the response to be in.</p> <p>Valid values are: <code>aws_v1</code> </p>', 'DescribeServicesResponse$FormatVersion' => '<p>The format version of the response. For example, <code>aws_v1</code>.</p>', 'GetProductsRequest$FormatVersion' => '<p>The format version that you want the response to be in.</p> <p>Valid values are: <code>aws_v1</code> </p>', 'GetProductsResponse$FormatVersion' => '<p>The format version of the response. For example, aws_v1.</p>', ], ], 'GetAttributeValuesRequest' => [ 'base' => NULL, 'refs' => [], ], 'GetAttributeValuesResponse' => [ 'base' => NULL, 'refs' => [], ], 'GetPriceListFileUrlRequest' => [ 'base' => NULL, 'refs' => [], ], 'GetPriceListFileUrlResponse' => [ 'base' => NULL, 'refs' => [], ], 'GetProductsRequest' => [ 'base' => NULL, 'refs' => [], ], 'GetProductsResponse' => [ 'base' => NULL, 'refs' => [], ], 'InternalErrorException' => [ 'base' => '<p>An error on the server occurred during the processing of your request. Try again later.</p>', 'refs' => [], ], 'InvalidNextTokenException' => [ 'base' => '<p>The pagination token is invalid. Try again without a pagination token.</p>', 'refs' => [], ], 'InvalidParameterException' => [ 'base' => '<p>One or more parameters had an invalid value.</p>', 'refs' => [], ], 'ListPriceListsRequest' => [ 'base' => NULL, 'refs' => [], ], 'ListPriceListsResponse' => [ 'base' => NULL, 'refs' => [], ], 'MaxResults' => [ 'base' => NULL, 'refs' => [ 'ListPriceListsRequest$MaxResults' => '<p>The maximum number of results to return in the response. </p>', ], ], 'NotFoundException' => [ 'base' => '<p>The requested resource can\'t be found.</p>', 'refs' => [], ], 'PriceList' => [ 'base' => '<p> <i> <b>This feature is in preview release and is subject to change. Your use of Amazon Web Services Price List API is subject to the Beta Service Participation terms of the <a href="https://aws.amazon.com/service-terms/">Amazon Web Services Service Terms</a> (Section 1.10).</b> </i> </p> <p>This is the type of price list references that match your request. </p>', 'refs' => [ 'PriceLists$member' => NULL, ], ], 'PriceListArn' => [ 'base' => NULL, 'refs' => [ 'GetPriceListFileUrlRequest$PriceListArn' => '<p>The unique identifier that maps to where your Price List files are located. <code>PriceListArn</code> can be obtained from the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_ListPriceLists.html">ListPriceLists</a> response. </p>', 'PriceList$PriceListArn' => '<p>The unique identifier that maps to where your Price List files are located. <code>PriceListArn</code> can be obtained from the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_ListPriceLists.html"> <code>ListPriceList</code> </a> response. </p>', ], ], 'PriceListJsonItems' => [ 'base' => NULL, 'refs' => [ 'GetProductsResponse$PriceList' => '<p>The list of products that match your filters. The list contains both the product metadata and the price information.</p>', ], ], 'PriceLists' => [ 'base' => NULL, 'refs' => [ 'ListPriceListsResponse$PriceLists' => '<p>The type of price list references that match your request. </p>', ], ], 'RegionCode' => [ 'base' => NULL, 'refs' => [ 'ListPriceListsRequest$RegionCode' => '<p>This is used to filter the Price List by Amazon Web Services Region. For example, to get the price list only for the <code>US East (N. Virginia)</code> Region, use <code>us-east-1</code>. If nothing is specified, you retrieve price lists for all applicable Regions. The available <code>RegionCode</code> list can be retrieved from <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_GetAttributeValues.html">GetAttributeValues</a> API.</p>', 'PriceList$RegionCode' => '<p>This is used to filter the Price List by Amazon Web Services Region. For example, to get the price list only for the <code>US East (N. Virginia)</code> Region, use <code>us-east-1</code>. If nothing is specified, you retrieve price lists for all applicable Regions. The available <code>RegionCode</code> list can be retrieved from <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_GetAttributeValues.html"> <code>GetAttributeValues</code> </a> API. </p>', ], ], 'ResourceNotFoundException' => [ 'base' => '<p>The requested resource can\'t be found.</p>', 'refs' => [], ], 'Service' => [ 'base' => '<p>The metadata for a service, such as the service code and available attribute names.</p>', 'refs' => [ 'ServiceList$member' => NULL, ], ], 'ServiceCode' => [ 'base' => NULL, 'refs' => [ 'ListPriceListsRequest$ServiceCode' => '<p>The service code or the Savings Plan service code for the attributes that you want to retrieve. For example, to get the list of applicable Amazon EC2 price lists, use <code>AmazonEC2</code>. For a full list of service codes containing On-Demand and Reserved Instance (RI) pricing, use the <a href="https://docs.aws.amazon.com/aws-cost-management/latest/APIReference/API_pricing_DescribeServices.html#awscostmanagement-pricing_DescribeServices-request-FormatVersion">DescribeServices</a> API.</p> <p>To retrieve the Reserved Instance and Compute Savings Plan price lists, use <code>ComputeSavingsPlans</code>. </p> <p>To retrieve Machine Learning Savings Plans price lists, use <code>MachineLearningSavingsPlans</code>. </p>', ], ], 'ServiceList' => [ 'base' => NULL, 'refs' => [ 'DescribeServicesResponse$Services' => '<p>The service metadata for the service or services in the response.</p>', ], ], 'String' => [ 'base' => NULL, 'refs' => [ 'AttributeNameList$member' => NULL, 'AttributeValue$Value' => '<p>The specific value of an <code>attributeName</code>.</p>', 'DescribeServicesRequest$ServiceCode' => '<p>The code for the service whose information you want to retrieve, such as <code>AmazonEC2</code>. You can use the <code>ServiceCode</code> to filter the results in a <code>GetProducts</code> call. To retrieve a list of all services, leave this blank.</p>', 'DescribeServicesRequest$NextToken' => '<p>The pagination token that indicates the next set of results that you want to retrieve.</p>', 'DescribeServicesResponse$NextToken' => '<p>The pagination token for the next set of retrievable results.</p>', 'GetAttributeValuesRequest$ServiceCode' => '<p>The service code for the service whose attributes you want to retrieve. For example, if you want the retrieve an EC2 attribute, use <code>AmazonEC2</code>.</p>', 'GetAttributeValuesRequest$AttributeName' => '<p>The name of the attribute that you want to retrieve the values for, such as <code>volumeType</code>.</p>', 'GetAttributeValuesRequest$NextToken' => '<p>The pagination token that indicates the next set of results that you want to retrieve.</p>', 'GetAttributeValuesResponse$NextToken' => '<p>The pagination token that indicates the next set of results to retrieve.</p>', 'GetPriceListFileUrlResponse$Url' => '<p>The URL to download your Price List file from. </p>', 'GetProductsRequest$ServiceCode' => '<p>The code for the service whose products you want to retrieve. </p>', 'GetProductsRequest$NextToken' => '<p>The pagination token that indicates the next set of results that you want to retrieve.</p>', 'GetProductsResponse$NextToken' => '<p>The pagination token that indicates the next set of results to retrieve.</p>', 'ListPriceListsRequest$NextToken' => '<p>The pagination token that indicates the next set of results that you want to retrieve. </p>', 'ListPriceListsResponse$NextToken' => '<p>The pagination token that indicates the next set of results to retrieve. </p>', 'Service$ServiceCode' => '<p>The code for the Amazon Web Services service.</p>', ], ], 'SynthesizedJsonPriceListJsonItem' => [ 'base' => NULL, 'refs' => [ 'PriceListJsonItems$member' => NULL, ], ], 'ThrottlingException' => [ 'base' => '<p>You\'ve made too many requests exceeding service quotas. </p>', 'refs' => [], ], 'Value' => [ 'base' => NULL, 'refs' => [ 'Filter$Value' => '<p>The service code or attribute value that you want to filter by. If you\'re filtering by service code this is the actual service code, such as <code>AmazonEC2</code>. If you\'re filtering by attribute name, this is the attribute value that you want the returned products to match, such as a <code>Provisioned IOPS</code> volume.</p>', ], ], 'errorMessage' => [ 'base' => NULL, 'refs' => [ 'AccessDeniedException$Message' => NULL, 'ExpiredNextTokenException$Message' => NULL, 'InternalErrorException$Message' => NULL, 'InvalidNextTokenException$Message' => NULL, 'InvalidParameterException$Message' => NULL, 'NotFoundException$Message' => NULL, 'ResourceNotFoundException$Message' => NULL, 'ThrottlingException$Message' => NULL, ], ], ],];

<?php
// This file was auto-generated from sdk-root/src/data/codestar-connections/2019-12-01/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2019-12-01', 'endpointPrefix' => 'codestar-connections', 'jsonVersion' => '1.0', 'protocol' => 'json', 'serviceFullName' => 'AWS CodeStar connections', 'serviceId' => 'CodeStar connections', 'signatureVersion' => 'v4', 'signingName' => 'codestar-connections', 'targetPrefix' => 'com.amazonaws.codestar.connections.CodeStar_connections_20191201', 'uid' => 'codestar-connections-2019-12-01', ], 'operations' => [ 'CreateConnection' => [ 'name' => 'CreateConnection', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateConnectionInput', ], 'output' => [ 'shape' => 'CreateConnectionOutput', ], 'errors' => [ [ 'shape' => 'LimitExceededException', ], ], ], 'DeleteConnection' => [ 'name' => 'DeleteConnection', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteConnectionInput', ], 'output' => [ 'shape' => 'DeleteConnectionOutput', ], 'errors' => [ [ 'shape' => 'ResourceNotFoundException', ], ], ], 'GetConnection' => [ 'name' => 'GetConnection', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'GetConnectionInput', ], 'output' => [ 'shape' => 'GetConnectionOutput', ], 'errors' => [ [ 'shape' => 'ResourceNotFoundException', ], ], ], 'ListConnections' => [ 'name' => 'ListConnections', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListConnectionsInput', ], 'output' => [ 'shape' => 'ListConnectionsOutput', ], ], ], 'shapes' => [ 'AccountId' => [ 'type' => 'string', 'max' => 12, 'min' => 12, 'pattern' => '[0-9]{12}', ], 'Connection' => [ 'type' => 'structure', 'members' => [ 'ConnectionName' => [ 'shape' => 'ConnectionName', ], 'ConnectionArn' => [ 'shape' => 'ConnectionArn', ], 'ProviderType' => [ 'shape' => 'ProviderType', ], 'OwnerAccountId' => [ 'shape' => 'AccountId', ], 'ConnectionStatus' => [ 'shape' => 'ConnectionStatus', ], ], ], 'ConnectionArn' => [ 'type' => 'string', 'max' => 256, 'min' => 0, 'pattern' => 'arn:aws(-[\\w]+)*:.+:.+:[0-9]{12}:.+', ], 'ConnectionList' => [ 'type' => 'list', 'member' => [ 'shape' => 'Connection', ], ], 'ConnectionName' => [ 'type' => 'string', 'max' => 32, 'min' => 1, ], 'ConnectionStatus' => [ 'type' => 'string', 'enum' => [ 'PENDING', 'AVAILABLE', 'ERROR', ], ], 'CreateConnectionInput' => [ 'type' => 'structure', 'required' => [ 'ProviderType', 'ConnectionName', ], 'members' => [ 'ProviderType' => [ 'shape' => 'ProviderType', ], 'ConnectionName' => [ 'shape' => 'ConnectionName', ], ], ], 'CreateConnectionOutput' => [ 'type' => 'structure', 'required' => [ 'ConnectionArn', ], 'members' => [ 'ConnectionArn' => [ 'shape' => 'ConnectionArn', ], ], ], 'DeleteConnectionInput' => [ 'type' => 'structure', 'required' => [ 'ConnectionArn', ], 'members' => [ 'ConnectionArn' => [ 'shape' => 'ConnectionArn', ], ], ], 'DeleteConnectionOutput' => [ 'type' => 'structure', 'members' => [], ], 'ErrorMessage' => [ 'type' => 'string', 'max' => 600, ], 'GetConnectionInput' => [ 'type' => 'structure', 'required' => [ 'ConnectionArn', ], 'members' => [ 'ConnectionArn' => [ 'shape' => 'ConnectionArn', ], ], ], 'GetConnectionOutput' => [ 'type' => 'structure', 'members' => [ 'Connection' => [ 'shape' => 'Connection', ], ], ], 'LimitExceededException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'exception' => true, ], 'ListConnectionsInput' => [ 'type' => 'structure', 'members' => [ 'ProviderTypeFilter' => [ 'shape' => 'ProviderType', ], 'MaxResults' => [ 'shape' => 'MaxResults', ], 'NextToken' => [ 'shape' => 'NextToken', ], ], ], 'ListConnectionsOutput' => [ 'type' => 'structure', 'members' => [ 'Connections' => [ 'shape' => 'ConnectionList', ], 'NextToken' => [ 'shape' => 'NextToken', ], ], ], 'MaxResults' => [ 'type' => 'integer', 'max' => 50, 'min' => 1, ], 'NextToken' => [ 'type' => 'string', 'max' => 1024, 'min' => 1, 'pattern' => '[a-zA-Z0-9=\\-\\\\/]+', ], 'ProviderType' => [ 'type' => 'string', 'enum' => [ 'Bitbucket', ], ], 'ResourceNotFoundException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'exception' => true, ], ],];

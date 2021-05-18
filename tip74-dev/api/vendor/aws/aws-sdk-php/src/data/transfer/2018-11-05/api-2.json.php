<?php
// This file was auto-generated from sdk-root/src/data/transfer/2018-11-05/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2018-11-05', 'endpointPrefix' => 'transfer', 'jsonVersion' => '1.1', 'protocol' => 'json', 'serviceAbbreviation' => 'AWS Transfer', 'serviceFullName' => 'AWS Transfer for SFTP', 'serviceId' => 'Transfer', 'signatureVersion' => 'v4', 'signingName' => 'transfer', 'targetPrefix' => 'TransferService', 'uid' => 'transfer-2018-11-05', ], 'operations' => [ 'CreateServer' => [ 'name' => 'CreateServer', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateServerRequest', ], 'output' => [ 'shape' => 'CreateServerResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceExistsException', ], ], ], 'CreateUser' => [ 'name' => 'CreateUser', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'CreateUserRequest', ], 'output' => [ 'shape' => 'CreateUserResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceExistsException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'DeleteServer' => [ 'name' => 'DeleteServer', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteServerRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'DeleteSshPublicKey' => [ 'name' => 'DeleteSshPublicKey', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteSshPublicKeyRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'DeleteUser' => [ 'name' => 'DeleteUser', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DeleteUserRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'DescribeServer' => [ 'name' => 'DescribeServer', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeServerRequest', ], 'output' => [ 'shape' => 'DescribeServerResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'DescribeUser' => [ 'name' => 'DescribeUser', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'DescribeUserRequest', ], 'output' => [ 'shape' => 'DescribeUserResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'ImportSshPublicKey' => [ 'name' => 'ImportSshPublicKey', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ImportSshPublicKeyRequest', ], 'output' => [ 'shape' => 'ImportSshPublicKeyResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceExistsException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'ListServers' => [ 'name' => 'ListServers', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListServersRequest', ], 'output' => [ 'shape' => 'ListServersResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidNextTokenException', ], [ 'shape' => 'InvalidRequestException', ], ], ], 'ListTagsForResource' => [ 'name' => 'ListTagsForResource', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListTagsForResourceRequest', ], 'output' => [ 'shape' => 'ListTagsForResourceResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidNextTokenException', ], [ 'shape' => 'InvalidRequestException', ], ], ], 'ListUsers' => [ 'name' => 'ListUsers', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'ListUsersRequest', ], 'output' => [ 'shape' => 'ListUsersResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidNextTokenException', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'StartServer' => [ 'name' => 'StartServer', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'StartServerRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'StopServer' => [ 'name' => 'StopServer', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'StopServerRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'TagResource' => [ 'name' => 'TagResource', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'TagResourceRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'TestIdentityProvider' => [ 'name' => 'TestIdentityProvider', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'TestIdentityProviderRequest', ], 'output' => [ 'shape' => 'TestIdentityProviderResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'UntagResource' => [ 'name' => 'UntagResource', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UntagResourceRequest', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], ], ], 'UpdateServer' => [ 'name' => 'UpdateServer', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateServerRequest', ], 'output' => [ 'shape' => 'UpdateServerResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceExistsException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'UpdateUser' => [ 'name' => 'UpdateUser', 'http' => [ 'method' => 'POST', 'requestUri' => '/', ], 'input' => [ 'shape' => 'UpdateUserRequest', ], 'output' => [ 'shape' => 'UpdateUserResponse', ], 'errors' => [ [ 'shape' => 'ServiceUnavailableException', ], [ 'shape' => 'InternalServiceError', ], [ 'shape' => 'InvalidRequestException', ], [ 'shape' => 'ResourceNotFoundException', ], [ 'shape' => 'ThrottlingException', ], ], ], ], 'shapes' => [ 'AddressAllocationId' => [ 'type' => 'string', ], 'AddressAllocationIds' => [ 'type' => 'list', 'member' => [ 'shape' => 'AddressAllocationId', ], ], 'Arn' => [ 'type' => 'string', 'max' => 1600, 'min' => 20, 'pattern' => 'arn:.*', ], 'ConflictException' => [ 'type' => 'structure', 'required' => [ 'Message', ], 'members' => [ 'Message' => [ 'shape' => 'Message', ], ], 'exception' => true, ], 'CreateServerRequest' => [ 'type' => 'structure', 'members' => [ 'EndpointDetails' => [ 'shape' => 'EndpointDetails', ], 'EndpointType' => [ 'shape' => 'EndpointType', ], 'HostKey' => [ 'shape' => 'HostKey', ], 'IdentityProviderDetails' => [ 'shape' => 'IdentityProviderDetails', ], 'IdentityProviderType' => [ 'shape' => 'IdentityProviderType', ], 'LoggingRole' => [ 'shape' => 'Role', ], 'Tags' => [ 'shape' => 'Tags', ], ], ], 'CreateServerResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'CreateUserRequest' => [ 'type' => 'structure', 'required' => [ 'Role', 'ServerId', 'UserName', ], 'members' => [ 'HomeDirectory' => [ 'shape' => 'HomeDirectory', ], 'HomeDirectoryType' => [ 'shape' => 'HomeDirectoryType', ], 'HomeDirectoryMappings' => [ 'shape' => 'HomeDirectoryMappings', ], 'Policy' => [ 'shape' => 'Policy', ], 'Role' => [ 'shape' => 'Role', ], 'ServerId' => [ 'shape' => 'ServerId', ], 'SshPublicKeyBody' => [ 'shape' => 'SshPublicKeyBody', ], 'Tags' => [ 'shape' => 'Tags', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'CreateUserResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'DateImported' => [ 'type' => 'timestamp', ], 'DeleteServerRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'DeleteSshPublicKeyRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'SshPublicKeyId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'SshPublicKeyId' => [ 'shape' => 'SshPublicKeyId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'DeleteUserRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'DescribeServerRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'DescribeServerResponse' => [ 'type' => 'structure', 'required' => [ 'Server', ], 'members' => [ 'Server' => [ 'shape' => 'DescribedServer', ], ], ], 'DescribeUserRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'DescribeUserResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'User', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'User' => [ 'shape' => 'DescribedUser', ], ], ], 'DescribedServer' => [ 'type' => 'structure', 'required' => [ 'Arn', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'EndpointDetails' => [ 'shape' => 'EndpointDetails', ], 'EndpointType' => [ 'shape' => 'EndpointType', ], 'HostKeyFingerprint' => [ 'shape' => 'HostKeyFingerprint', ], 'IdentityProviderDetails' => [ 'shape' => 'IdentityProviderDetails', ], 'IdentityProviderType' => [ 'shape' => 'IdentityProviderType', ], 'LoggingRole' => [ 'shape' => 'Role', ], 'ServerId' => [ 'shape' => 'ServerId', ], 'State' => [ 'shape' => 'State', ], 'Tags' => [ 'shape' => 'Tags', ], 'UserCount' => [ 'shape' => 'UserCount', ], ], ], 'DescribedUser' => [ 'type' => 'structure', 'required' => [ 'Arn', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'HomeDirectory' => [ 'shape' => 'HomeDirectory', ], 'HomeDirectoryMappings' => [ 'shape' => 'HomeDirectoryMappings', ], 'HomeDirectoryType' => [ 'shape' => 'HomeDirectoryType', ], 'Policy' => [ 'shape' => 'Policy', ], 'Role' => [ 'shape' => 'Role', ], 'SshPublicKeys' => [ 'shape' => 'SshPublicKeys', ], 'Tags' => [ 'shape' => 'Tags', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'EndpointDetails' => [ 'type' => 'structure', 'members' => [ 'AddressAllocationIds' => [ 'shape' => 'AddressAllocationIds', ], 'SubnetIds' => [ 'shape' => 'SubnetIds', ], 'VpcEndpointId' => [ 'shape' => 'VpcEndpointId', ], 'VpcId' => [ 'shape' => 'VpcId', ], ], ], 'EndpointType' => [ 'type' => 'string', 'enum' => [ 'PUBLIC', 'VPC', 'VPC_ENDPOINT', ], ], 'HomeDirectory' => [ 'type' => 'string', 'max' => 1024, 'pattern' => '^$|/.*', ], 'HomeDirectoryMapEntry' => [ 'type' => 'structure', 'required' => [ 'Entry', 'Target', ], 'members' => [ 'Entry' => [ 'shape' => 'MapEntry', ], 'Target' => [ 'shape' => 'MapTarget', ], ], ], 'HomeDirectoryMappings' => [ 'type' => 'list', 'member' => [ 'shape' => 'HomeDirectoryMapEntry', ], 'max' => 50, 'min' => 1, ], 'HomeDirectoryType' => [ 'type' => 'string', 'enum' => [ 'PATH', 'LOGICAL', ], ], 'HostKey' => [ 'type' => 'string', 'max' => 4096, 'sensitive' => true, ], 'HostKeyFingerprint' => [ 'type' => 'string', ], 'IdentityProviderDetails' => [ 'type' => 'structure', 'members' => [ 'Url' => [ 'shape' => 'Url', ], 'InvocationRole' => [ 'shape' => 'Role', ], ], ], 'IdentityProviderType' => [ 'type' => 'string', 'enum' => [ 'SERVICE_MANAGED', 'API_GATEWAY', ], ], 'ImportSshPublicKeyRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'SshPublicKeyBody', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'SshPublicKeyBody' => [ 'shape' => 'SshPublicKeyBody', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'ImportSshPublicKeyResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'SshPublicKeyId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'SshPublicKeyId' => [ 'shape' => 'SshPublicKeyId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'InternalServiceError' => [ 'type' => 'structure', 'required' => [ 'Message', ], 'members' => [ 'Message' => [ 'shape' => 'Message', ], ], 'exception' => true, 'fault' => true, ], 'InvalidNextTokenException' => [ 'type' => 'structure', 'required' => [ 'Message', ], 'members' => [ 'Message' => [ 'shape' => 'Message', ], ], 'exception' => true, ], 'InvalidRequestException' => [ 'type' => 'structure', 'required' => [ 'Message', ], 'members' => [ 'Message' => [ 'shape' => 'Message', ], ], 'exception' => true, ], 'ListServersRequest' => [ 'type' => 'structure', 'members' => [ 'MaxResults' => [ 'shape' => 'MaxResults', ], 'NextToken' => [ 'shape' => 'NextToken', ], ], ], 'ListServersResponse' => [ 'type' => 'structure', 'required' => [ 'Servers', ], 'members' => [ 'NextToken' => [ 'shape' => 'NextToken', ], 'Servers' => [ 'shape' => 'ListedServers', ], ], ], 'ListTagsForResourceRequest' => [ 'type' => 'structure', 'required' => [ 'Arn', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'MaxResults' => [ 'shape' => 'MaxResults', ], 'NextToken' => [ 'shape' => 'NextToken', ], ], ], 'ListTagsForResourceResponse' => [ 'type' => 'structure', 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'NextToken' => [ 'shape' => 'NextToken', ], 'Tags' => [ 'shape' => 'Tags', ], ], ], 'ListUsersRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'MaxResults' => [ 'shape' => 'MaxResults', ], 'NextToken' => [ 'shape' => 'NextToken', ], 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'ListUsersResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'Users', ], 'members' => [ 'NextToken' => [ 'shape' => 'NextToken', ], 'ServerId' => [ 'shape' => 'ServerId', ], 'Users' => [ 'shape' => 'ListedUsers', ], ], ], 'ListedServer' => [ 'type' => 'structure', 'required' => [ 'Arn', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'IdentityProviderType' => [ 'shape' => 'IdentityProviderType', ], 'EndpointType' => [ 'shape' => 'EndpointType', ], 'LoggingRole' => [ 'shape' => 'Role', ], 'ServerId' => [ 'shape' => 'ServerId', ], 'State' => [ 'shape' => 'State', ], 'UserCount' => [ 'shape' => 'UserCount', ], ], ], 'ListedServers' => [ 'type' => 'list', 'member' => [ 'shape' => 'ListedServer', ], ], 'ListedUser' => [ 'type' => 'structure', 'required' => [ 'Arn', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'HomeDirectory' => [ 'shape' => 'HomeDirectory', ], 'HomeDirectoryType' => [ 'shape' => 'HomeDirectoryType', ], 'Role' => [ 'shape' => 'Role', ], 'SshPublicKeyCount' => [ 'shape' => 'SshPublicKeyCount', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'ListedUsers' => [ 'type' => 'list', 'member' => [ 'shape' => 'ListedUser', ], ], 'MapEntry' => [ 'type' => 'string', 'max' => 1024, 'pattern' => '^/.*', ], 'MapTarget' => [ 'type' => 'string', 'max' => 1024, 'pattern' => '^/.*', ], 'MaxResults' => [ 'type' => 'integer', 'max' => 1000, 'min' => 1, ], 'Message' => [ 'type' => 'string', ], 'NextToken' => [ 'type' => 'string', 'max' => 6144, 'min' => 1, ], 'NullableRole' => [ 'type' => 'string', 'max' => 2048, 'pattern' => '^$|arn:.*role/.*', ], 'Policy' => [ 'type' => 'string', 'max' => 2048, ], 'Resource' => [ 'type' => 'string', ], 'ResourceExistsException' => [ 'type' => 'structure', 'required' => [ 'Message', 'Resource', 'ResourceType', ], 'members' => [ 'Message' => [ 'shape' => 'Message', ], 'Resource' => [ 'shape' => 'Resource', ], 'ResourceType' => [ 'shape' => 'ResourceType', ], ], 'exception' => true, ], 'ResourceNotFoundException' => [ 'type' => 'structure', 'required' => [ 'Message', 'Resource', 'ResourceType', ], 'members' => [ 'Message' => [ 'shape' => 'Message', ], 'Resource' => [ 'shape' => 'Resource', ], 'ResourceType' => [ 'shape' => 'ResourceType', ], ], 'exception' => true, ], 'ResourceType' => [ 'type' => 'string', ], 'Response' => [ 'type' => 'string', ], 'RetryAfterSeconds' => [ 'type' => 'string', ], 'Role' => [ 'type' => 'string', 'max' => 2048, 'min' => 20, 'pattern' => 'arn:.*role/.*', ], 'ServerId' => [ 'type' => 'string', 'max' => 19, 'min' => 19, 'pattern' => '^s-([0-9a-f]{17})$', ], 'ServiceErrorMessage' => [ 'type' => 'string', ], 'ServiceUnavailableException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ServiceErrorMessage', ], ], 'exception' => true, 'fault' => true, 'synthetic' => true, ], 'SshPublicKey' => [ 'type' => 'structure', 'required' => [ 'DateImported', 'SshPublicKeyBody', 'SshPublicKeyId', ], 'members' => [ 'DateImported' => [ 'shape' => 'DateImported', ], 'SshPublicKeyBody' => [ 'shape' => 'SshPublicKeyBody', ], 'SshPublicKeyId' => [ 'shape' => 'SshPublicKeyId', ], ], ], 'SshPublicKeyBody' => [ 'type' => 'string', 'max' => 2048, 'pattern' => '^ssh-rsa\\s+[A-Za-z0-9+/]+[=]{0,3}(\\s+.+)?\\s*$', ], 'SshPublicKeyCount' => [ 'type' => 'integer', ], 'SshPublicKeyId' => [ 'type' => 'string', 'max' => 21, 'min' => 21, 'pattern' => '^key-[0-9a-f]{17}$', ], 'SshPublicKeys' => [ 'type' => 'list', 'member' => [ 'shape' => 'SshPublicKey', ], 'max' => 5, ], 'StartServerRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'State' => [ 'type' => 'string', 'enum' => [ 'OFFLINE', 'ONLINE', 'STARTING', 'STOPPING', 'START_FAILED', 'STOP_FAILED', ], ], 'StatusCode' => [ 'type' => 'integer', ], 'StopServerRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'SubnetId' => [ 'type' => 'string', ], 'SubnetIds' => [ 'type' => 'list', 'member' => [ 'shape' => 'SubnetId', ], ], 'Tag' => [ 'type' => 'structure', 'required' => [ 'Key', 'Value', ], 'members' => [ 'Key' => [ 'shape' => 'TagKey', ], 'Value' => [ 'shape' => 'TagValue', ], ], ], 'TagKey' => [ 'type' => 'string', 'max' => 128, ], 'TagKeys' => [ 'type' => 'list', 'member' => [ 'shape' => 'TagKey', ], 'max' => 50, 'min' => 1, ], 'TagResourceRequest' => [ 'type' => 'structure', 'required' => [ 'Arn', 'Tags', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'Tags' => [ 'shape' => 'Tags', ], ], ], 'TagValue' => [ 'type' => 'string', 'max' => 256, ], 'Tags' => [ 'type' => 'list', 'member' => [ 'shape' => 'Tag', ], 'max' => 50, 'min' => 1, ], 'TestIdentityProviderRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'UserName' => [ 'shape' => 'UserName', ], 'UserPassword' => [ 'shape' => 'UserPassword', ], ], ], 'TestIdentityProviderResponse' => [ 'type' => 'structure', 'required' => [ 'StatusCode', 'Url', ], 'members' => [ 'Response' => [ 'shape' => 'Response', ], 'StatusCode' => [ 'shape' => 'StatusCode', ], 'Message' => [ 'shape' => 'Message', ], 'Url' => [ 'shape' => 'Url', ], ], ], 'ThrottlingException' => [ 'type' => 'structure', 'members' => [ 'RetryAfterSeconds' => [ 'shape' => 'RetryAfterSeconds', ], ], 'exception' => true, ], 'UntagResourceRequest' => [ 'type' => 'structure', 'required' => [ 'Arn', 'TagKeys', ], 'members' => [ 'Arn' => [ 'shape' => 'Arn', ], 'TagKeys' => [ 'shape' => 'TagKeys', ], ], ], 'UpdateServerRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'EndpointDetails' => [ 'shape' => 'EndpointDetails', ], 'EndpointType' => [ 'shape' => 'EndpointType', ], 'HostKey' => [ 'shape' => 'HostKey', ], 'IdentityProviderDetails' => [ 'shape' => 'IdentityProviderDetails', ], 'LoggingRole' => [ 'shape' => 'NullableRole', ], 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'UpdateServerResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], ], ], 'UpdateUserRequest' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'UserName', ], 'members' => [ 'HomeDirectory' => [ 'shape' => 'HomeDirectory', ], 'HomeDirectoryType' => [ 'shape' => 'HomeDirectoryType', ], 'HomeDirectoryMappings' => [ 'shape' => 'HomeDirectoryMappings', ], 'Policy' => [ 'shape' => 'Policy', ], 'Role' => [ 'shape' => 'Role', ], 'ServerId' => [ 'shape' => 'ServerId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'UpdateUserResponse' => [ 'type' => 'structure', 'required' => [ 'ServerId', 'UserName', ], 'members' => [ 'ServerId' => [ 'shape' => 'ServerId', ], 'UserName' => [ 'shape' => 'UserName', ], ], ], 'Url' => [ 'type' => 'string', 'max' => 255, ], 'UserCount' => [ 'type' => 'integer', ], 'UserName' => [ 'type' => 'string', 'max' => 32, 'min' => 3, 'pattern' => '^[a-zA-Z0-9_][a-zA-Z0-9_-]{2,31}$', ], 'UserPassword' => [ 'type' => 'string', 'max' => 2048, 'sensitive' => true, ], 'VpcEndpointId' => [ 'type' => 'string', 'max' => 22, 'min' => 22, 'pattern' => '^vpce-[0-9a-f]{17}$', ], 'VpcId' => [ 'type' => 'string', ], ],];

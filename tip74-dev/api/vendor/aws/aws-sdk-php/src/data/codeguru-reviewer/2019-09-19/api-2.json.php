<?php
// This file was auto-generated from sdk-root/src/data/codeguru-reviewer/2019-09-19/api-2.json
return [ 'version' => '2.0', 'metadata' => [ 'apiVersion' => '2019-09-19', 'endpointPrefix' => 'codeguru-reviewer', 'jsonVersion' => '1.1', 'protocol' => 'rest-json', 'serviceAbbreviation' => 'CodeGuruReviewer', 'serviceFullName' => 'Amazon CodeGuru Reviewer', 'serviceId' => 'CodeGuru Reviewer', 'signatureVersion' => 'v4', 'signingName' => 'codeguru-reviewer', 'uid' => 'codeguru-reviewer-2019-09-19', ], 'operations' => [ 'AssociateRepository' => [ 'name' => 'AssociateRepository', 'http' => [ 'method' => 'POST', 'requestUri' => '/associations', ], 'input' => [ 'shape' => 'AssociateRepositoryRequest', ], 'output' => [ 'shape' => 'AssociateRepositoryResponse', ], 'errors' => [ [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'DescribeRepositoryAssociation' => [ 'name' => 'DescribeRepositoryAssociation', 'http' => [ 'method' => 'GET', 'requestUri' => '/associations/{AssociationArn}', ], 'input' => [ 'shape' => 'DescribeRepositoryAssociationRequest', ], 'output' => [ 'shape' => 'DescribeRepositoryAssociationResponse', ], 'errors' => [ [ 'shape' => 'NotFoundException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'DisassociateRepository' => [ 'name' => 'DisassociateRepository', 'http' => [ 'method' => 'DELETE', 'requestUri' => '/associations/{AssociationArn}', ], 'input' => [ 'shape' => 'DisassociateRepositoryRequest', ], 'output' => [ 'shape' => 'DisassociateRepositoryResponse', ], 'errors' => [ [ 'shape' => 'NotFoundException', ], [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ValidationException', ], [ 'shape' => 'AccessDeniedException', ], [ 'shape' => 'ConflictException', ], [ 'shape' => 'ThrottlingException', ], ], ], 'ListRepositoryAssociations' => [ 'name' => 'ListRepositoryAssociations', 'http' => [ 'method' => 'GET', 'requestUri' => '/associations', ], 'input' => [ 'shape' => 'ListRepositoryAssociationsRequest', ], 'output' => [ 'shape' => 'ListRepositoryAssociationsResponse', ], 'errors' => [ [ 'shape' => 'InternalServerException', ], [ 'shape' => 'ValidationException', ], [ 'shape' => 'ThrottlingException', ], ], ], ], 'shapes' => [ 'AccessDeniedException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'error' => [ 'httpStatusCode' => 403, ], 'exception' => true, ], 'Arn' => [ 'type' => 'string', 'max' => 1600, 'min' => 1, 'pattern' => '^arn:aws[^:\\s]*:codeguru-reviewer:[^:\\s]+:[\\d]{12}:[a-z]+:[\\w-]+$', ], 'AssociateRepositoryRequest' => [ 'type' => 'structure', 'required' => [ 'Repository', ], 'members' => [ 'Repository' => [ 'shape' => 'Repository', ], 'ClientRequestToken' => [ 'shape' => 'ClientRequestToken', 'idempotencyToken' => true, ], ], ], 'AssociateRepositoryResponse' => [ 'type' => 'structure', 'members' => [ 'RepositoryAssociation' => [ 'shape' => 'RepositoryAssociation', ], ], ], 'AssociationId' => [ 'type' => 'string', 'max' => 64, 'min' => 1, ], 'ClientRequestToken' => [ 'type' => 'string', 'max' => 64, 'min' => 1, 'pattern' => '^[\\w-]+$', ], 'CodeCommitRepository' => [ 'type' => 'structure', 'required' => [ 'Name', ], 'members' => [ 'Name' => [ 'shape' => 'Name', ], ], ], 'ConflictException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'error' => [ 'httpStatusCode' => 409, ], 'exception' => true, ], 'DescribeRepositoryAssociationRequest' => [ 'type' => 'structure', 'required' => [ 'AssociationArn', ], 'members' => [ 'AssociationArn' => [ 'shape' => 'Arn', 'location' => 'uri', 'locationName' => 'AssociationArn', ], ], ], 'DescribeRepositoryAssociationResponse' => [ 'type' => 'structure', 'members' => [ 'RepositoryAssociation' => [ 'shape' => 'RepositoryAssociation', ], ], ], 'DisassociateRepositoryRequest' => [ 'type' => 'structure', 'required' => [ 'AssociationArn', ], 'members' => [ 'AssociationArn' => [ 'shape' => 'Arn', 'location' => 'uri', 'locationName' => 'AssociationArn', ], ], ], 'DisassociateRepositoryResponse' => [ 'type' => 'structure', 'members' => [ 'RepositoryAssociation' => [ 'shape' => 'RepositoryAssociation', ], ], ], 'ErrorMessage' => [ 'type' => 'string', ], 'InternalServerException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'error' => [ 'httpStatusCode' => 500, ], 'exception' => true, 'fault' => true, ], 'ListRepositoryAssociationsRequest' => [ 'type' => 'structure', 'members' => [ 'ProviderTypes' => [ 'shape' => 'ProviderTypes', 'location' => 'querystring', 'locationName' => 'ProviderType', ], 'States' => [ 'shape' => 'RepositoryAssociationStates', 'location' => 'querystring', 'locationName' => 'State', ], 'Names' => [ 'shape' => 'Names', 'location' => 'querystring', 'locationName' => 'Name', ], 'Owners' => [ 'shape' => 'Owners', 'location' => 'querystring', 'locationName' => 'Owner', ], 'MaxResults' => [ 'shape' => 'MaxResults', 'location' => 'querystring', 'locationName' => 'MaxResults', ], 'NextToken' => [ 'shape' => 'NextToken', 'location' => 'querystring', 'locationName' => 'NextToken', ], ], ], 'ListRepositoryAssociationsResponse' => [ 'type' => 'structure', 'members' => [ 'RepositoryAssociationSummaries' => [ 'shape' => 'RepositoryAssociationSummaries', ], 'NextToken' => [ 'shape' => 'NextToken', ], ], ], 'MaxResults' => [ 'type' => 'integer', 'max' => 100, 'min' => 1, ], 'Name' => [ 'type' => 'string', 'max' => 100, 'min' => 1, ], 'Names' => [ 'type' => 'list', 'member' => [ 'shape' => 'Name', ], 'max' => 3, 'min' => 1, ], 'NextToken' => [ 'type' => 'string', 'max' => 2048, 'min' => 1, ], 'NotFoundException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'error' => [ 'httpStatusCode' => 404, ], 'exception' => true, ], 'Owner' => [ 'type' => 'string', 'max' => 100, 'min' => 1, ], 'Owners' => [ 'type' => 'list', 'member' => [ 'shape' => 'Owner', ], 'max' => 3, 'min' => 1, ], 'ProviderType' => [ 'type' => 'string', 'enum' => [ 'CodeCommit', 'GitHub', ], ], 'ProviderTypes' => [ 'type' => 'list', 'member' => [ 'shape' => 'ProviderType', ], 'max' => 3, 'min' => 1, ], 'Repository' => [ 'type' => 'structure', 'members' => [ 'CodeCommit' => [ 'shape' => 'CodeCommitRepository', ], ], ], 'RepositoryAssociation' => [ 'type' => 'structure', 'members' => [ 'AssociationId' => [ 'shape' => 'AssociationId', ], 'AssociationArn' => [ 'shape' => 'Arn', ], 'Name' => [ 'shape' => 'Name', ], 'Owner' => [ 'shape' => 'Owner', ], 'ProviderType' => [ 'shape' => 'ProviderType', ], 'State' => [ 'shape' => 'RepositoryAssociationState', ], 'StateReason' => [ 'shape' => 'StateReason', ], 'LastUpdatedTimeStamp' => [ 'shape' => 'TimeStamp', ], 'CreatedTimeStamp' => [ 'shape' => 'TimeStamp', ], ], ], 'RepositoryAssociationState' => [ 'type' => 'string', 'enum' => [ 'Associated', 'Associating', 'Failed', 'Disassociating', ], ], 'RepositoryAssociationStates' => [ 'type' => 'list', 'member' => [ 'shape' => 'RepositoryAssociationState', ], 'max' => 3, 'min' => 1, ], 'RepositoryAssociationSummaries' => [ 'type' => 'list', 'member' => [ 'shape' => 'RepositoryAssociationSummary', ], ], 'RepositoryAssociationSummary' => [ 'type' => 'structure', 'members' => [ 'AssociationArn' => [ 'shape' => 'Arn', ], 'LastUpdatedTimeStamp' => [ 'shape' => 'TimeStamp', ], 'AssociationId' => [ 'shape' => 'AssociationId', ], 'Name' => [ 'shape' => 'Name', ], 'Owner' => [ 'shape' => 'Owner', ], 'ProviderType' => [ 'shape' => 'ProviderType', ], 'State' => [ 'shape' => 'RepositoryAssociationState', ], ], ], 'StateReason' => [ 'type' => 'string', 'max' => 256, 'min' => 0, ], 'ThrottlingException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'error' => [ 'httpStatusCode' => 429, ], 'exception' => true, ], 'TimeStamp' => [ 'type' => 'timestamp', ], 'ValidationException' => [ 'type' => 'structure', 'members' => [ 'Message' => [ 'shape' => 'ErrorMessage', ], ], 'error' => [ 'httpStatusCode' => 400, ], 'exception' => true, ], ],];

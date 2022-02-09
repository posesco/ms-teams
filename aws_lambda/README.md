## How to use

This project includes the creation of a lambda function with the Terraform tool, its operation is intended to inform about the changes suffered by an AWS S3 bucket.

It is enough to do a terraform apply -auto-approve as long as you have your aws-cli configured.

## Providers

| Name | Version |
|------|---------|
| <a name="provider_aws"></a> [aws](#provider\_aws) | n/a |

## Modules

No modules.

## Resources

| Name | Type |
|------|------|
| [aws_iam_role.notifier](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/iam_role) | resource |
| [aws_lambda_function.ms_teams](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/resources/lambda_function) | resource |
| [aws_iam_policy.policy](https://registry.terraform.io/providers/hashicorp/aws/latest/docs/data-sources/iam_policy) | data source |

## Inputs

| Name | Description | Type | Default | Required |
|------|-------------|------|---------|:--------:|
| <a name="input_environment"></a> [environment](#input\_environment) | The environment Name | `string` | `"develop"` | no |
| <a name="input_function_name"></a> [function\_name](#input\_function\_name) | Lambda function name | `string` | `"alert_msteams"` | no |
| <a name="input_project"></a> [project](#input\_project) | The project Name | `string` | `"notifier-ms_teams"` | no |
| <a name="input_type"></a> [type](#input\_type) | Type of infrastructure | `string` | `"app"` | no |

## Outputs

| Name | Description |
|------|-------------|
| <a name="output_iam_role"></a> [iam\_role](#output\_iam\_role) | n/a |
| <a name="output_lambda_func"></a> [lambda\_func](#output\_lambda\_func) | n/a |

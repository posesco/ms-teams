terraform {
  backend "s3" {
    bucket = "back-logs-wiedii"
    key    = "lambda/notifier-ms_teams.tfstate"
    region = "us-east-1"
  }
}

provider "aws" {
  region = "us-east-1"
}

data "aws_iam_policy" "policy" {
  name = "AWSLambdaBasicExecutionRole"
}

resource "aws_iam_role" "notifier" {
  name                = "notifier-ms_teams-role"
  description         = "Role with permissions to use lambda function"
  path                = "/custom-role/"
  tags                = local.common_tags
  managed_policy_arns = [data.aws_iam_policy.policy.arn]
  assume_role_policy = jsonencode({
    Version = "2012-10-17"
    Statement = [
      {
        Action = "sts:AssumeRole"
        Effect = "Allow"
        Principal = {
          Service = "lambda.amazonaws.com"
        }
      },
    ]
  })
}

resource "aws_lambda_function" "ms_teams" {
  description      = "Notifications MS-Teams"
  filename         = "lambda_function.zip"
  function_name    = "ms_teams"
  handler          = "lambda_function.lambda_handler"
  package_type     = "Zip"
  role             = aws_iam_role.notifier.arn
  runtime          = "python3.8"
  source_code_hash = filebase64sha256("lambda_function.zip")
  tags             = local.common_tags

  tracing_config {
    mode = "PassThrough"
  }
}

locals {
  common_tags = {
    terraform   = "true"
    project     = "${var.project}"
    environment = "${var.environment}"
    type        = "${var.type}"
  }
}
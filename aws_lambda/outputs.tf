output "iam_role" {
  value = aws_iam_role.notifier.arn
}

output "lambda_func" {
  value = aws_lambda_function.ms_teams.arn
}
variable "function_name" {
  description = "Lambda function name"
  type        = string
  default     = "alert_msteams"
}

variable "type" {
  description = "Type of infrastructure"
  type        = string
  default     = "app"
}

variable "project" {
  description = "The project Name"
  type        = string
  default     = "notifier-ms_teams"
}

variable "environment" {
  description = "The environment Name"
  type        = string
  default     = "develop"
}
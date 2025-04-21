# Database Schema Overview

## Table: `users`

| Column Name    | Data Type |
|----------------|-----------|
| employee_id    | INTEGER   |
| name           | TEXT      |
| email          | TEXT      |
| password_hash  | TEXT      |
| is_admin       | INTEGER   |
| profile_image  | TEXT      |
| phone          | TEXT      |
| department     | TEXT      |
| manager_email  | TEXT      |
| leave_quota    | INTEGER   |
| leave_used     | INTEGER   |
| date_created   | TEXT      |
| last_login     | TEXT      |
| rfid_id        | TEXT      |

## Table: `sqlite_sequence`

| Column Name | Data Type |
|-------------|-----------|
| name        |           |
| seq         |           |

## Table: `leaves`

| Column Name     | Data Type |
|-----------------|-----------|
| leave_id        | INTEGER   |
| employee_id     | INTEGER   |
| from_date       | TEXT      |
| to_date         | TEXT      |
| reason          | TEXT      |
| manager_email   | TEXT      |
| status          | TEXT      |
| date_requested  | TEXT      |

## Table: `timesheet`

| Column Name  | Data Type |
|--------------|-----------|
| id           | INTEGER   |
| employee_id  | INTEGER   |
| clock_in     | TEXT      |
| clock_out    | TEXT      |
| note         | TEXT      |

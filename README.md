# Project Approval Workflow System

A Laravel-based Project Approval Workflow System that allows users to submit projects and administrators to review, approve, or reject them.

## Features

- User authentication
- Role-based access control
- Project submission
- Project file upload
- Project approval and rejection
- Rejection reason
- Bulk approve and reject
- Email notifications
- Queued emails
- Audit logs
- MySQL stored procedure for project approval
- REST API
- Laravel Sanctum authentication
- Server-side DataTables
- Project status tracking
- Dashboard with project statistics

## Tech Stack

- Laravel
- PHP
- MySQL
- Bootstrap
- JavaScript
- Laravel Sanctum
- Yajra DataTables
- MySQL Stored Procedures
- Laravel Queue
- Laravel Mail

## Project Workflow

1. User logs into the application.
2. User submits a project with title, description, and optional file.
3. Project is created with `Pending` status.
4. Admin can review submitted projects.
5. Admin can approve or reject a project.
6. Rejected projects can include a rejection reason.
7. Email notification is sent to the project submitter.
8. Project actions are recorded in audit logs.

## Video Walkthroughs

### Project Demo

[Watch Project Demo Video](https://drive.google.com/file/d/1alKePF8CwJYO_eZYDSkSxTGwB51xxx45/view?usp=sharing)

### Code Walkthrough

[Watch Code Explanation Video](https://drive.google.com/file/d/1oV8VBPPqBDv7B7RbR5esTwgDzs8hhaii/view?usp=sharing)

### API Walkthrough

[Watch API Code & Testing Video](https://drive.google.com/file/d/1jHVsoNk4d3URvYdFtB3nERYNwiK9QPZd/view?usp=sharing)

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/devvishukashyap/project-approval-workflow.git
cd project-approval-workflow

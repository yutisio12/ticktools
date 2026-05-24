# IT Ticketing System - Project Specification

## Project Overview

Build an internal IT Ticketing System to support:
- IT Support Operations
- Incident Management
- Request Management
- Change Management
- SLA Monitoring
- IT Staff KPI Monitoring
- Team Performance Monitoring
- Ticket Review & Approval Workflow

The system must be:
- scalable
- maintainable
- audit-ready
- realtime
- KPI-oriented
- easy to use for Users, IT Staff, and IT Leads

---

# User Roles

## 1. User

Permissions:
- Login to the system
- Create tickets
- Upload attachments
- Select related assets
- View ticket status
- View ticket timeline
- Receive notifications
- Reopen tickets within allowed timeframe

---

## 2. IT Staff

Permissions:
- View available tickets
- Claim tickets
- Set ticket priority
- Set ticket impact
- Update ticket status
- Add work activity/progress
- Fill root cause analysis
- Fill ticket resolution
- Submit completed ticket for review

---

## 3. IT Lead

Permissions:
- Review completed tickets
- Approve or return tickets
- Monitor SLA compliance
- Monitor individual and team KPI
- Override ticket weight/score if necessary
- Access weekly and monthly dashboards

---

## 4. Administrator

Permissions:
- Manage users
- Manage roles and permissions
- Manage ticket categories
- Manage subcategories
- Manage SLA configuration
- Manage asset master data
- Manage notification settings
- Manage overall system configuration

---

# Authentication

## Login Method

Users authenticate using:
- Badge / Employee ID
- Date of Birth
- Captcha

Future enhancement:
- LDAP
- Active Directory
- SSO integration

---

# Ticket Categories

The system supports 3 main ticket categories:
- Request
- Incident
- Change

---

# Ticket Creation Fields

## User Input Fields

When creating a ticket, users must provide:
- Title
- Description
- Category
- Attachment(s)
- Related Asset

---

## IT Staff Input Fields

After claiming a ticket, IT Staff must define:
- Priority
- Impact
- Status
- Activity Progress
- Resolution
- Root Cause
- Prevention

---

# Priority Levels

- Low
- Medium
- High
- Critical

---

# Impact Levels

- Personal
- Department
- Company Wide

---

# Ticket Status Workflow

## Main Ticket Statuses

1. Open
2. Assigned
3. In Progress
4. Pending User
5. Pending Vendor
6. Pending Review
7. Returned
8. Closed

---

# Ticket Workflow

## 1. User Creates Ticket
- User submits a ticket
- Initial status: Open
- Notification sent to IT Support team

---

## 2. IT Staff Claims Ticket
- IT Staff selects and claims ticket
- Status changes to Assigned

---

## 3. IT Staff Starts Working
- Status changes to In Progress
- Start date is automatically generated

---

## 4. Activity Updates

IT Staff can add multiple activity logs:
- Date
- Activity note
- Progress percentage
- Duration
- Optional attachment

Activity logs are used for:
- audit trail
- KPI tracking
- reporting
- troubleshooting history

---

## 5. Ticket Resolution

IT Staff must complete:
- Resolution
- Root Cause
- Prevention
- End Date

After completion:
- status changes to Pending Review

---

## 6. IT Lead Review

IT Lead can:
- Approve ticket
- Return ticket for revision

If approved:
- status changes to Closed

If returned:
- status changes to Returned

---

## 7. Ticket Reopen

Users may reopen tickets:
- within 3 or 7 days after closure
- if the issue is not fully resolved

---

# Notification System

## Notification Types

### Realtime Notification
- In-app notification
- WebSocket-based realtime updates

### Email Notification

Trigger events:
- Ticket created
- Ticket assigned
- Ticket updated
- Ticket returned
- Ticket closed
- SLA warning notification

---

# SLA Management

## SLA Based On
- Ticket Priority
- Ticket Category

---

## SLA Example Matrix

| Priority | SLA |
|---|---|
| Critical | 4 Hours |
| High | 1 Day |
| Medium | 3 Days |
| Low | 5 Days |

---

# KPI System

## KPI Metrics

### 1. Total Tickets Completed
Total number of completed tickets.

### 2. Total Ticket Weight
Total accumulated ticket score.

### 3. SLA Compliance
Percentage of tickets completed within SLA.

### 4. Return Rate
Percentage of tickets returned by IT Lead.

### 5. Average Resolution Time
Average ticket completion duration.

### 6. Overdue Tickets
Total tickets exceeding SLA.

---

# Ticket Weighting System

## Weight Calculation Formula

Ticket score is automatically calculated based on:
- Category
- Priority
- Impact

Formula:

```text
Ticket Weight = Base Category Score + Priority Score + Impact Score
# invoices-microservices
Project 1/6 Using Microservices

Frontend: Vue 3
Backend: Node & Laravel as microservices.

# Invoice Management System - Microservices

Invoice Management System for accounting software, based on modern architecture:

## Services
- Backend Laravel: Invoicing, tax calculation, storage, and PDF generation.
- Backend Node.js: Notification service (email/SMS).
- Gateway API (Node.js): Orchestration and routing.
- Frontend Vue 3: SPA for invoice management.

## Technologies
- Docker (Docker Desktop + WSL2)
- Vue 3, Node.js, Laravel 10
- MySQL (compatible with AWS RDS)
- GitHub Actions (CI/CD)
- RabbitMQ, Grafana, Prometheus (advanced deployment)
- Kubernetes/EKS (AWS Free tier, future stages)

## Basic Diagram

Frontend <-> Gateway <-> Laravel (Invoices)
                     |
             Node.js Notifications
                     |
                 Email/SMS
                 
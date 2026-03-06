# Multilead - Multi-Tenant CRM & Website Management

> **Note:** This repository is a practical portfolio project, developed to demonstrate my proficiency in software architecture, system design, and advanced backend development. The main focus is not just delivering a functional application, but writing clean, scalable, testable code grounded in the best practices of software engineering and the PHP ecosystem, applying software development best practices to solve real problems and needs of a real estate management business focused on sales, leads, and digital marketing.

## Project Overview

Multilead is a flexible **Multi-Tenancy** and CRM platform designed to manage customer relationships, lead automation, and features for multiple websites simultaneously — operating on a single codebase and a logically isolated database.

The construction of this project aims to demonstrate and apply:
- **High-Complexity Architectures:** Meticulous relational modeling with perfect isolation in the *multi-tenancy* model.
- **Design Patterns & SOLID:** Responsible use of design patterns (Repository, Factory, Observer, among others) and strict adherence to SOLID principles, aiming for extremely high maintainability (high cohesion and low coupling), taking due care not to create unnecessary overengineering, focusing on fast and scalable solutions.
- **Clean Code:** Clearly divided responsibilities, strict typing of objects and methods, focusing on the natural semantics of code readability.
- **Strict Documentation Standards:** Every new feature MUST follow the [Feature Spec Template](./docs/features/000-feature-spec-template.md) before implementation starts.

## Technical & Architectural Highlights

The topics below consolidate my engineering decisions adopted in this repository:

### 1. Architecture and Data Security
- **Robust and Secure Multi-Tenancy:** The entire project handles the separation of tenant scopes. The strict hierarchy (Tenant/Company -> Branches/Sites -> Modules) shields the system against cross-tenant data leakage and orphaned records.
- **Highly Modular Ecosystem:** Dynamic business models (such as Banners, Blogs, and Product Portfolios) operate with a modular architecture, allowing easy activation, deactivation, or extension of these entities smoothly according to each business's needs.
- **Audit Trail:** Implementation and customization of the `Spatie ActivityLog` package to maintain the history of modifications ("before" and "after" the payload). Logs automatically map events to their respective *tenant*, regardless of the depth of child instances in the database.

### 2. Technology Stack and Modern Tools
- **PHP 8.2+:** Intentional exploration of modern language features, such as advanced typing, native enums, *readonly* properties, and *constructor property promotion*.
- **Laravel 12:** The backend engine, aligned with the official documentation for adopting dynamic Providers, modernized routes, Form Requests (local injection), advanced Eloquent (polymorphic relationships), and native optimizations.
- **Filament v4 & Livewire 3:** Deep utilization of FilamentPHP's *Server-Driven UI* (SDUI) architecture supported by Livewire's modern websockets. This enables reactive, componentized, and high-performance backend panels without taxing resources on isolated REST APIs/SPAs.
- **PostgreSQL:** The main database for guaranteed manipulation of relational modeling combined with `JSONB` fields. Data with highly mutable keys and dynamic SEO configurations (Analytics, inserted headers) live in transactional metadata columns.
- **Docker:** No manual dependencies on the host. 100% implemented in containers with strictly versioned and as lightweight images as possible.

### 3. Developer Experience (DX) and Standards
- **Complete Fake Environment (Seeders/Factories):** All entities are covered by logically chained *factories*, making it possible to recreate everything from a simple database record to an entire populated scenario of users, leads, and rules in a single command.
- **Absolute PSR-12 Standardization:** Natively integrated into the ecosystem via **Laravel Pint**, ensuring universal reading compliance across the entire *codebase*.
- **Granular Documentation & Specs:** Every core feature is documented *before* implementation using a granular approach (Feature Docs). These documents serve as technical contracts, containing Gherkin-style scenarios and Mermaid diagrams, directly guiding the TDD cycle and ensuring that architecture and implementation are always in sync.

---

## Differentiated Installation (Development Setup)

👉 [Read the Docs for Infrastructure & Setup Guide](./docs/infraestructure.md)

# Multilead - Enterprise Multi-Tenant CRM & Real Estate Engine

> **Engineering Note:** This repository is a high-level portfolio project designed to demonstrate advanced proficiency in software architecture, system design, and modern backend engineering. Beyond delivering a functional SaaS, the core objective is to showcase a **production-ready codebase** built on the pillars of scalability, maintainable patterns, and a strict Test-Driven Development (TDD) culture.

---

## Architectural Philosophy: The Modular Monolith

- Domain-Driven Design (DDD) Influence: Features are treated as independent internal modules inside app/Modules/ with high cohesion. We utilize the Action Pattern to encapsulate complex workflows, keeping Models thin and the domain logic pure.
- Event-Driven Decoupling: Modules communicate primarily through Domain Events, ensuring that a change in the 'Leads' module doesn't trigger a cascade of failures in 'Clients'.

## The TALL Stack & Modern Product Engineering

We utilize the **TALL Stack (Tailwind, Alpine.js, Laravel 12, Livewire 3)** augmented by **Filament v4**.

- **Server-Driven UI (SDUI):** Leveraging Filament allows us to build complex, reactive interfaces entirely in PHP. This drastically reduces the overhead of maintaining decoupled SPAs and complex REST API state synchronization while delivering a high-performance, fluid UX.
- **Product Velocity without Debt:** This stack is chosen for maximum engineering efficiency—reaching the market quickly without sacrificing the robustness of a server-rendered application.

## Engineering Excellence & Quality Assurance

Seniority is defined by the reliability of the code. This project enforces a **Testing-First** mindset:

- **Strict TDD Cycle:** All business logic is driven by automated tests in `tests/`. We verify behavioral correctness before implementation, ensuring that complex multi-tenant relationships remain unbreakable during refactoring.
- **Design Patterns in Practice:** Adopts **Services, Actions, and Observers** to avoid "Fat Controllers." We use strongly typed **Enums** for data boundaries and **Shared Schemas** in Filament to ensure strict DRY (Don't Repeat Yourself) compliance.
- **PostgreSQL & JSONB:** Utilizing PostgreSQL's relational power combined with JSONB for flexible metadata, balancing structured data integrity with the agility needed for dynamic SEO and site configurations.

## Documentation as Code

Documentation is not an afterthought; it is a technical contract. Using a **Granular Documentation** strategy:
- Every core feature follows a strict specification template (Gherkin scenarios + Mermaid diagrams) *before* the first line of code is written.
- These specs align 1:1 with PHPUnit Feature Tests, creating a "living documentation" that stays in sync with the codebase.

---

## Getting Started (Development Setup)

To quickly set up the containerized environment and see the engineering in action:

[Infrastructure & Setup Guide](./docs/infraestructure.md)

---

## Deep Dive into the Architecture

To explore the project's inner workings and access comprehensive documentation, please refer to:


[/docs - Architecture Decisions & Feature Specs](./docs/architecture/architecture_decisions.md)

---

## 🤝 Let's Connect

[Thiago Cardoso Silva - LinkedIn](https://www.linkedin.com/in/thiagocardososilva/)

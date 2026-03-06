# Architecture Decisions & System Design

This document outlines the core architectural choices, technology stack rationales, and the overall structural design of the Multilead platform. It serves as a guide for understanding _why_ certain patterns were chosen and _where_ to find things within the codebase.

## The TALL Stack (Tailwind, Alpine.js, Laravel, and Livewire)

The decision to use the TALL stack, specifically augmented with **Filament v4**, was driven by the need for high development velocity without sacrificing UX or backend performance.

- **Laravel (The Backend Engine):** Chosen for its robust ecosystem, expressive syntax, and built-in solutions for complex problems (Queues, Event Broadcasting, robust ORM).
- **Livewire & Alpine.js (The Reactive Layer):** Instead of building a decoupled SPA (Single Page Application) with React or Vue, which inherently requires maintaining two separate codebases and managing complex state synchronization via REST APIs, Livewire brings reactivity directly to the backend. It drastically reduces context switching for developers and is ideal for projects that can bypass unnecessary complexity to reach the market quickly, making it a perfect fit for MVPs and fast-paced product development.
- **Filament (The UI Framework):** Deeply utilizes Server-Driven UI (SDUI). It allows us to build complex, highly interactive data tables, forms, and analytical dashboards using pure PHP, abstracting away massive amounts of boilerplate frontend code.

This stack proves that you can achieve SPA-like fluidity and modern reactivity while keeping the architectural simplicity and security of a server-rendered application.

## Dual-Panel Architecture (Filament)

As the application is strictly modeled after the real-world operational needs of a real estate business, it demands separate, highly focused user interfaces rather than a single messy dashboard. We implement this cleanly by using two distinct Filament panels, each powered by its own dedicated Service Provider:

- **Admin Panel (`AdminPanelProvider`):** Dedicated to internal system administration, contractor management, and high-level platform configurations.
- **App Panel (`AppPanelProvider`):** The client-facing portal designed strictly for the end-users (the real estate operations agents and managers).

This clear separation of concerns ensures that the administrative back-office and the customer-facing SaaS environments remain secure, logically autonomous, independently customizable, and totally decoupled from each other.

## The Modular Monolith Approach

Multilead is intentionally designed as a **Modular Monolith**. While microservices are heavily discussed in modern engineering, starting with them often introduces premature complexity (network latency, distributed transactions, complex CI/CD, and data consistency issues). 

Instead, our approach embraces modularity *within* a single deployment unit:

- **High Cohesion, Low Coupling:** Features (like CRM, Blog, and Banners) are treated as distinct internal modules. They communicate through well-defined interfaces or event-driven patterns rather than deep, tangled dependencies.
- **Ready for Extraction:** Why modularize a monolith? Because different parts of a system have vastly different scaling and resource requirements. If, in the future, the 'Lead Processing' engine requires massive horizontal scaling due to high throughput, while the 'Admin Panel' only needs standard vertical scaling, the lead engine's code is already decoupled enough to be extracted into a standalone microservice with as minimal refactoring as possible.
- **Team Topology:** As engineering teams grow, a modular monolith allows different squads to take ownership of specific domains (e.g., Squad CRM vs. Squad Websites) within the same repository without stepping on each other's toes, avoiding merge hell.

## Scalability Considerations

To demonstrate an understanding of systems at scale, the architecture anticipates both Vertical and Horizontal scaling strategies:

- **Vertical Scaling (Scaling Up):** The application is deeply optimized (e.g., using `JSONB` in PostgreSQL for flexible schema requirements, preventing massive table joining overhead) to perform exceptionally well even on single, powerful nodes.
- **Horizontal Scaling (Scaling Out):** The system is stateless. Docker containerization, coupled with externalized session management and centralized queuing/caching (Redis compatibility is assumed in the design), ensures that we can spin up identical application nodes behind a load balancer to handle increased traffic seamlessly.

## Test-Driven Development (TDD), Decoupling & Patterns

We strictly avoid the "Fat Controller" anti-pattern and mandate thorough testing. Business logic is meticulously decoupled from HTTP delivery mechanisms:

- **TDD Philosophy:** Development strictly adopts **Test-Driven Development (TDD)** paradigms. Features originate from automated tests assuring behavioral correctness, ensuring regressions are immediately caught when dealing with profound relationships.
- **Repositories / Services:** Controllers and Livewire components are lean; their only job is to handle requests and return responses. Heavy lifting is delegated to dedicated Service/Action classes.
- **Observers & Events:** Side effects (like firing an email or logging an audit trail when a Lead is created) are handled via Eloquent Observers or event listeners. This adheres to the Single Responsibility Principle (SRP)—the logic that models the lead shouldn't care about the logging mechanism.

## Granular Feature Documentation (Specifications as Code)

To bridge the gap between business requirements and technical implementation, we employ a **Granular Documentation** strategy located in `docs/features/`.

- **MANDATORY TEMPLATE:** Every new feature specification must be based on the [000-feature-spec-template.md](./000-feature-spec-template.md).
- **Specification over Ambiguitiy:** No feature is built without a corresponding `.md` specification. Each doc contains Gherkin scenarios (Given/When/Then), Technical Specs (Hooks, Models, Guards), and Mermaid Sequence Diagrams.
- **Contract-First Development:** These documents act as technical contracts. They prevent "vague implementations" by forcing the architect and developer to agree on logic flow, data boundaries, and failure cases before a single line of application code is written.
- **Micro-Docs for Maintainability:** Instead of a single, monolithic (and often outdated) system manual, we maintain small, hyper-focused files. This makes it trivial to update documentation during refactoring and ensures that documentation evolves at the same pace as the code.
- **Direct TDD Alignment:** The scenarios defined in these docs translate 1:1 into our PHPUnit Feature Tests, ensuring that what is documented is what is actually being verified by the CI/CD pipeline.

---

## Codebase Navigation: Where are things?

To maintain sanity in a growing codebase, the directory structure is heavily customized and organized by domain context where applicable, rather than strictly by file type.

- `app/Enums/`: Strongly typed enumerations defining system constants (e.g., Lead Statuses, Role Types). They are used heavily to enforce valid data boundaries at the programmatic level.
- `app/Models/`: Defines the Eloquent ORM representations and their complex relational mappings (polymorphic, multi-tenant scopes).
- `app/Filament/`: Contains the entire Server-Driven UI logic, elegantly split between the respective `App` and `Admin` clusters.
  - *Note on Schemas:* Certain form and table components are reused identically across different panels. These shared schemas are located directly in `app/Filament/Schemas/` (outside the scope of individual panels) to guarantee strict DRY (Don't Repeat Yourself) compliance.
- `app/Observers/`: Where model lifecycle hooks are placed to keep models and controllers clean of side-effect logic.
- `app/Policies/`: Authorization logic ensuring rigorous tenant-isolation checks before any action is permitted.
- `database/migrations/`: The history of the database schema.
- `database/seeders/` & `database/factories/`: The mock data generation engine, crucial for setting up the local Dev environment realistically.
- `routes/`: Standard HTTP routing, split logically (e.g., `web.php` for site delivery, console routes).
- `tests/`: Home of the automated testing suite. Because we use TDD, this directory dictates strict business rule verification through mock-injecting and unit/feature assertions.
- `docker/`: Houses the Docker configurations dictating containerization bounds, creating a flawlessly reproducible runtime.
- `docs/`: Where this and other architectural documentation reside.

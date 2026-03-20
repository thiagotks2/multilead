# ADR 001: Refactoring to a Modular Monolith Architecture

## Status
In Progress

## Context and Problem Statement
The current application architecture mixes different business domains (Clients, Leads, Properties) within the standard Laravel MVC structure. As the Multilead platform grows to support complex multi-tenant operations, managing enterprise real estate logic in a classic monolith risks creating a "Big Ball of Mud". This leads to high coupling, unintended side effects when modifying cross-domain logic, and merge conflicts between teams. We need a structural plan to manage scaling without jumping prematurely into microservices.

## Decision Drivers
- Need for clear boundaries between different business domains (e.g., separating Lead distribution logic from CRM/Client management).
- Need to prevent accidental database cross-writes between domains.
- Preparation for future scalability if a specific bounded context (like Lead Routing) requires extraction into a microservice.
- Maintain high development velocity without the immediate infrastructure complexity of distributed microservices.

## Decision Outcome
We decided to refactor the application into a **Pragmatic Modular Monolith**. This involves isolating each business domain into its own Bounded Context within `app/Modules/{ModuleName}/`.
- Each module will physically contain its own `Models`, `Actions`, and `Events`.
- Modules will communicate exclusively through Domain Events or Public Actions.
- `app/Core/` will hold module-agnostic logic.
- Filament panels (Admin and App) will act solely as presentation layers (Bounded UI), invoking Module Actions for any logic exceeding basic CRUD.

## Consequences
- **Positive:** Increased maintainability, forced architectural discipline, and easier testing mapped 1:1 with modular domains.
- **Negative:** A slight increase in boilerplate and initial setup time compared to basic Laravel MVC. Requires strict developer adherence to the new structural conventions to prevent backsliding into tight coupling.

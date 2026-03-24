# ADR 001: Refactoring to a Modular Monolith Architecture

## Status
Accepted

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

### Current Modules
The following Bounded Contexts are currently implemented as a result of the refactoring process:

1. **Audit (app/Modules/Audit/)**
   - **Contents:** `Models`.
   - **Purpose:** Handles application activity logging and platform observability (such as complying with `spatie/laravel-activitylog`), keeping tracking logic separated from fundamental domain data.

2. **CRM (app/Modules/CRM/)**
   - **Contents:** `Models`, `Actions`, `Enums`.
   - **Purpose:** Represents Customer Relationship Management domains, tackling complex logic around managing interactions, properties, or leads. The `Actions` extract heavy workflows from Models.

3. **Clients (app/Modules/Clients/)**
   - **Contents:** `Models`, `Actions`.
   - **Purpose:** Serves the core tenant base of the platform (e.g., real estate firms, brokers). It encapsulates managing subscriptions, features, or the distinct customers using the SaaS product.

4. **Identity (app/Modules/Identity/)**
   - **Contents:** `Models`, `Actions`, `Enums`, `Observers`, `Services`.
   - **Purpose:** Governs Users, Roles, Permissions, and general Authentication. Keeps system access control decoupled so that security validations and rules do not leak into other functional domains.

5. **Websites (app/Modules/Websites/)**
   - **Contents:** `Models`, `Actions`, `Enums`, `Policies`.
   - **Purpose:** Controls the external-facing instances or real estate portals, domains, themes, and configurations produced by Multilead for its clients.

## Consequences
- **Positive:** Increased maintainability, forced architectural discipline, and easier testing mapped 1:1 with modular domains.
- **Negative:** A slight increase in boilerplate and initial setup time compared to basic Laravel MVC. Requires strict developer adherence to the new structural conventions to prevent backsliding into tight coupling.

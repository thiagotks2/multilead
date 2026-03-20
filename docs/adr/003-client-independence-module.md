# ADR 004: Isolating Clients into an Independent Bounded Context (Module)

## Status
Accepted

## Context and Problem Statement
Following the decision to adopt a Modular Monolith architecture (ADR 001) and to physically separate Leads from Clients (ADR 003), we needed to determine the exact module boundaries via Domain-Driven Design concepts. Should Clients be grouped inside a general "Customer Service/Sales" (Atendimento) module alongside Leads, or should they possess their own dedicated Module?

## Decision Drivers
- Need for high cohesion. The rules governing a CRM Client (exclusivity rules, tenant sharing boundaries, strict multi-tenant validation, profile enrichment) are dense and isolated.
- The "Sales/Atendimento" domain primarily focuses on fast-paced workflows: lead routing logic, answering fast inquiries, tracking SLAs, and qualifying.
- Clients act as a central pillar for multiple future domains (e.g., Financial, Contracts, Marketing). Tangling "Clients" specifically with "Atendimento" would create cyclical dependencies later when the Finance module needs client data but has no relation to sales routing.

## Considered Options
1. **Unified `Atendimento` Module:** Group `Leads`, `Clients`, and `Service Desk` in one broad Bounded Context.
2. **Dedicated `Clients` Module:** Extract CRM core (`Clients`) into its own Bounded Context, strictly decoupled from the operational `Leads/Atendimento` flow.

## Decision Outcome
We chose **Option 2: Dedicated `Clients` Module**.
- Clients will reside in `app/Modules/Clients/`, establishing a pure CRM boundary.
- Leads and the immediate qualification workflows will reside in a separate operational module (e.g., `app/Modules/Atendimento/` or `app/Modules/Leads/`).
- The Client module acts as a foundational, core domain. Other modules (like Atendimento) can refer to it, but the Clients module itself remains completely agnostic of how the original lead was captured or routed.

## Consequences
- **Positive:** Prevents the "Atendimento" module from becoming bloated with CRM rules. Allows the `Clients` module to be safely required by billing/contracting domains later without dragging operational lead routing logic along.
- **Negative:** Requires formal Inter-Module communication protocol. For example, a Domain Event or a Cross-Module Action must be invoked when a Lead qualifies in the Atendimento module to trigger the physical creation of the Client in the Clients module.

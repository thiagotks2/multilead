# C4 Model: Level 1 - System Context

This document provides the high-level context of the Multilead platform, mapping out the interactions between the various users (actors) and the external systems required for its operation.

```mermaid
C4Context
    title System Context (Level 1) - Multilead

    %% Actors
    Person(admin, "Admin / Corporate", "Global Administrator. Manages tenants, global business rules, and the master administrative panel.")
    Person(agent, "Agent / Broker", "Tenant User. Uses the panel to manage their client portfolio, leads, and real estate properties.")
    
    %% Main System
    System(multilead, "Multilead", "Enterprise Multi-Tenant CRM & Real Estate Engine. Orchestrates leads, portfolios, real estate displays, and multi-tenancy.")
    
    %% External Systems
    System_Ext(portais, "Real Estate Portals", "ZAP, VivaReal, etc. (Optional). Sources or destinations for publishing properties/leads.")

    %% Relationships
    Rel(admin, multilead, "Configures and manages platform via", "HTTPS")
    Rel(agent, multilead, "Registers properties, handles leads via", "HTTPS")
    
    
    Rel(multilead, portais, "Imports leads or exports/synchronizes properties via", "XML / APIs API")
```

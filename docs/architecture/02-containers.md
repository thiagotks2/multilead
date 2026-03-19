# C4 Model: Level 2 - Containers

This document dives deeper into the internal building blocks (Containers) of the Multilead platform, demonstrating the separation between the UI interfaces (App/Admin), the Core Modules, and the Database.

```mermaid
C4Container
    title Container Diagram (Level 2) - Multilead

    Person(admin, "Admin / Corporate", "Accesses master backend panel.")
    Person(agent, "Agent / Broker", "Accesses tenant operation panel.")
    Person(lead, "Lead / End Client", "Visits public sites or landing pages.")
    
    System_Boundary(multilead, "Multilead Engine") {
        
        Container(admin_panel, "Admin Panel", "Laravel + Filament v4", "Global administrative panel. Manages configurations, tenants, and system logs (SDUI).")
        Container(app_panel, "App Panel", "Laravel + Filament v4", "Agent/Broker operational panel. Strict scope and isolated from the master.")
        Container(public_site, "Web / Public Storefront", "Laravel + Livewire 3 + Tailwind", "Dynamic Server-Side Rendered (SSR) applications focused on SEO and lead capture.")
        
        Container(core_modules, "Core & Modules Domain", "PHP / Laravel", "Architectural base (app/Modules). Encapsulates pure domain Actions, Listeners, and Business Logic (Modular Monolith).")
        
        ContainerDb(database, "PostgreSQL Database", "PostgreSQL 15 + pgvector", "Strict relational storage. Uses JSONB for flexible metadata (preventing generic column accumulation) and vectors for potential semantic search.")
    }
    
    System_Ext(email_provider, "E-mail Provider", "External sender.")
    
    %% Relationships Panels -> Core
    Rel(admin, admin_panel, "Administrates tenant and accesses via", "HTTPS")
    Rel(agent, app_panel, "Manages business within their scope via", "HTTPS")
    Rel(lead, public_site, "Searches catalog via", "HTTPS")
    
    Rel(admin_panel, core_modules, "Uses global and administrative Actions")
    Rel(app_panel, core_modules, "Uses tenant-scoped Actions (Filament Action Limits)")
    Rel(public_site, core_modules, "Consumes cached queries for display")
    
    Rel(core_modules, database, "Reads and writes data utilizing", "Eloquent ORM")
    Rel(core_modules, email_provider, "Fires events and queued jobs for email sending")
```

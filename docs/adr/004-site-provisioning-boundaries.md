# ADR 004: Site Provisioning and Management Boundaries

## Status
Accepted

## Context and Problem Statement
In the Multilead platform, corporate clients (tenants) can have external-facing Websites (e.g., real estate portals, landing pages). Allowing end users (brokers or real estate agency owners) to independently provision new websites on demand introduces security, billing, and infrastructural risks. We must decide how to manage the lifecycle of a `Site`—specifically, who can create or delete it, and who is allowed to manage its content and configuration.

## Decision Drivers
- **Billing & Infrastructure Control:** A new site requires domain configuration, SSL provisioning, and tracking of resources. This must be strictly controlled by the platform administrators.
- **Client Autonomy:** Once a site is provisioned, the client needs the autonomy to manage its content (banners, posts, SEO data, and visual settings) without constantly opening support tickets.
- **Panel Separation:** The system has two distinct interaction layers: the **Admin Panel** (used by Multilead staff) and the **App Panel** (used by tenants/clients).

## Decision Outcome
We decided on a strict **Asymmetric Permissions Model** for the Site entity.
1. **Creation and Deletion are strictly reserved for Platform Admins:**
   - A tenant cannot create or delete a `Site` under any permission rule.
   - This prevents shadow provisioning and ensures proper onboarding/offboarding of digital assets.
2. **Shared Management:**
   - Once a site exists, its management (editing data, adding banners, changing SMTP, or adjusting SEO) is fully accessible to the tenant within the App Panel, as long as they have the basic domain rights.
   - Platform Admins can also impersonate or access these forms via the Admin Panel to assist the client.

To enforce this, we tied the restriction directly to the interaction layer (the panel being used) rather than writing complex user-level ACLs. If an action like `create`, `delete`, `forceDelete`, or `restore` originates from the client-facing context, it is automatically blocked.

## Consequences
- **Positive:** Maximum security and control over infrastructure and billing events. Simplifies the permission matrix, as we do not need to check if a regular user has a "create site" permission—it is inherently denied.
- **Negative:** If a client upgrades their subscription to include a new site, the provisioning process remains manual or requires an internal operational workflow by the Multilead team, rather than being completely self-service.

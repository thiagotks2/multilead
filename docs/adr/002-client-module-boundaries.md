# ADR 003: Separation of Leads and Clients into Distinct Entities

## Status
Accepted

## Context and Problem Statement
When modeling the CRM aspect of the real estate engine, we had to decide how to represent individuals interacting with the company. The core architectural question was: Is a "Lead" the same entity as a "Client" (just holding a different state/status in the database), or do they represent fundamentally different lifecycles and data shapes that justify separate database tables?

## Decision Drivers
- Leads are high-volume, fast-moving, and often contain massive amounts of spam, unverified data, or incomplete contacts.
- Mixing unverified web contacts with actual negotiated clients pollutes the master CRM database and inflates indexing sizes.
- The lifecycle of a lead (capture, routing/distribution, qualification, spam filtering) is highly distinct from the lifecycle of a client (ongoing deal, historic purchases, rich profile building).
- Optimizing database scans when trying to locate true "Clients" amidst thousands of dead leads.

## Considered Options
1. **Single Entity (`contacts` or `customers`) with a Status Flag:** A single table where an entry is marked as `status: 'lead'` and later transition to `status: 'client'`.
2. **Distinct Entities in Separate Tables:** A `leads` table handling raw, high-volume entries, and a `clients` table containing verified people undergoing business relationships.

## Decision Outcome
We chose **Option 2: Distinct Entities in Separate Tables**.
- **Leads:** Serve as a high-volume, ephemeral gateway. They are captured, distributed by routing logic, and vetted. If determined to be spam or unqualified, they are discarded without ever touching the CRM core.
- **Clients:** Only when a lead is verified (not spam) and is actively participating in a business relationship/service, their core data is promoted and saved into the `clients` table.
- This creates a clear physical barrier, protecting the integrity and quality of the `clients` database from front-line internet noise.

## Consequences
- **Positive:** Protects query performance and data integrity of the `clients` table. Simplifies the state machines and data retention policies, allowing old dead leads to be bulk-purged without risking actual client data.
- **Negative:** Requires mapping data and copying fields (name, email, phone) from the `leads` table to the `clients` table upon conversion, leading to slight data duplication at the moment of promotion.

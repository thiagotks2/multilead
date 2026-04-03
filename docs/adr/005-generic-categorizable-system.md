# ADR 006: Generic Categorizable Architecture

## Status
Accepted

## Context and Problem Statement
In a complex real estate and CRM platform, the concept of a "Category" extends beyond a single domain (like a blog). A corporate client might want a broad category (e.g., "Luxury Properties" or "Pet Friendly Area") that acts as an umbrella grouping **both** a specific set of properties (`Imovel`) and specific blog articles (`SitePost`) related to that topic. 

If we lock categories strictly to posts via a rigid foreign key (or have the Post be the center of the polymorphic relation), we lose this cross-domain tagging potential and force duplication of categories across different modules.

## Decision Drivers
- **Centralized Taxonomy:** We need a single source of truth for taxonomy tags belonging to a `Site`.
- **Entity Agnosticism:** The category should be the "center of the universe", capable of grouping multiple distinct entity types without altering the database schema each time we add a new taggable entity (Posts, Banners, Properties, Leads).
- **Isolation:** The category must belong to a `Site` so that tenants do not pollute each other's taxonomy trees.

## Decision Outcome
We decided to implement a **Centralized Generic Categorizable System** structurally defined by two main entities: `SiteCategory` and a polymorphic pivot `site_categorizables`.

1. **The Core Entity (`SiteCategory`):**
   - It belongs to a `Site`. It holds the basic definition (`name`, `slug`, `description`, `seo_settings`).
   - It also contains a `type` string column (e.g., `general`, `post`, `property`, `banner`). This allows the platform to know if a category was created exclusively for a specific domain, or if it is meant to aggregate items globally across the site.
2. **The Pivot Table (`site_categorizables`):**
   - This table links the Category to any other model.
   - It uses `site_category_id` (Fixed Foreign Key to the category).
   - It uses `categorizable_id` and `categorizable_type` (Polymorphic Keys pointing to the target entity, like `SitePost` or `Imovel`).
3. **Usage:**
   - Any model that needs to be categorized simply implements a `morphedByMany` or `morphToMany` trait pointing to `SiteCategory` through the `site_categorizables` table. No new pivot migrations will be required to tag new future entities.

*(Note: This supersedes early drafts where `SitePostCategory` restricted taxonomy only to blog items and where the pivot table was inverted, centering around the Post).*

## Consequences
- **Positive:** Massive architectural flexibility. A single Category URL slug on the front-end can seamlessly aggregate different types of content (a page showing the "Pet Friendly" properties intermingled with "Pet Friendly" blog tips).
- **Negative:** Slightly more complex Eloquent queries compared to standard rigid `BelongsToMany` pivot tables, as the ORM must resolve the `categorizable_type` string. Requires developers to ensure they query the correct `type` scope if they only want to load Post-specific categories in a UI dropdown.

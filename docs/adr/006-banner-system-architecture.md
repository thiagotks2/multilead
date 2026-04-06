# ADR 006: Banner System Architecture

## Status
Accepted

## Context and Problem Statement
Corporate clients need a way to easily update hero images, advertisements, or call-to-action blocks on their external-facing Websites. Hardcoding these images requires developer intervention. A purely CMS-driven page builder is too complex for our current MVP and forces a heavy learning curve on the user. We need a flexible, lightweight way to inject dynamic banners categorized by behavior.

## Decision Drivers
- **Simplicity for the User:** The client should only need to upload an image, provide a link, title, description, and select the banner type.
- **Flexibility for the Themes:** Different website themes handle each banner type differently (e.g., popups vs. static banners).
- **UI Consistency:** The management interface should be intuitive, using tabs for quick filtering by type.

## Decision Outcome
We decided to implement a **single-entity Banner System** using `SiteBanner` with a `BannerType` enum for classification.

### Banner Type Enum (`BannerType`)
Banner types are fixed values defined in code as a PHP Backed Enum. They are not managed in the database, ensuring simplicity and predictability. Each value carries a distinct behavioral meaning for the front-end rendering layer:

| Value | Label | Description |
|---|---|---|
| `general` | General | A static banner with no specific behavioral trigger. Suitable for hero sections, promotional images, or highlights. |
| `entry_popup` | Entry Popup | A floating modal/popup displayed as soon as the user enters the site. Used for announcements or promotional offers on first visit. |
| `exit_intent` | Exit Intent | A popup triggered when the system detects the user is about to leave the page (mouse moves toward browser chrome). Used for last-chance offers or lead capture. |

Adding a new type in the future requires a code change and deploy — this is intentional and acceptable for the current product stage.

### Image Storage Strategy
- Banner images are stored **locally** on the same application server.
- The physical path structure follows the convention: `{company_id}/{site_id}/banners/{hash}-{site_id}-{company_id}.{ext}`.
- Accepted file extensions: **png** and **jpg** only.
- Image filenames must be unique hashes within the folder, suffixed by site ID and company ID separated by hyphens (e.g., `a3f9c2e1-42-7.jpg`).

### Image Optimization Pipeline
- After upload, an **Observer** on the `SiteBanner` model dispatches an `ImageOptimizationRequested` event.
- A dedicated service (`ImageOptimizerService`), documented in ADR 017, handles the optimization asynchronously via a queued job.
- See [ADR 017: Image Optimization Service](017-image-optimizer-service.md) for processing rules.

### UI Implementation Strategy (Filament v4)
- `BannerResource` is a **simple resource** (modal-based CRUD, created with `--simple`).
- The Banner list features **Dynamic Tabs**: "All" + one tab per `BannerType` enum value.
- Tabs filter the table by the banner's `type` column instantly via Livewire.

## Consequences
- **Positive:** No extra table or management UI needed for types. Extremely predictable for front-end rendering — the front-end queries "give me active banners of type `entry_popup`" and renders accordingly.
- **Negative:** Adding a new banner type requires a code deploy. Acceptable for the current product stage; can be revisited if tenant-managed custom types become a requirement.

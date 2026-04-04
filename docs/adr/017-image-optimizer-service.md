# ADR 017: Image Optimization Service

## Status
Accepted

## Context and Problem Statement
Multiple modules across the platform (Banners, and potentially Posts and Properties in the future) need to optimize images upon upload. Without a centralized service, each module would implement its own optimization logic, leading to code duplication, inconsistent behavior, and difficulty maintaining quality standards over time. We need a single, shared service that any module can trigger whenever an image needs optimization.

## Decision Drivers
- **DRY Principle:** Image optimization must be a cross-cutting concern handled in one place.
- **Performance:** Optimization must be non-blocking for the user (asynchronous via queued jobs).
- **Simplicity:** The format must remain widely compatible (PNG/JPG) for the immediate use case, without forcing WebP conversion.
- **Storage Integrity:** Original files must be overwritten in-place after optimization, keeping the same path.

## Decision Outcome
We decided to implement a centralized **`ImageOptimizerService`** located in `app/Core/Services/ImageOptimizerService.php`.

### Module Placement
Because image optimization is a **cross-cutting concern** not bound to any single domain, it lives in `app/Core/Services/`, not inside a specific Module. Any module can use it.

### Trigger Mechanism (Observer + Event)
- When an image-holding model is created (e.g., `SiteBanner`), its **Observer** dispatches an `ImageOptimizationRequested` event.
- A **Listener** picks up the event and dispatches a queued **`OptimizeImageJob`** which calls `ImageOptimizerService::optimize()`.
- **BR07 (Async Execution):** Optimization is always dispatched as a queued job. The HTTP response is never blocked by processing.
- **BR08 (Directory Auto-Creation):** Before writing the optimized file, the service must ensure the target directory exists. If it does not, it must be created recursively with appropriate permissions (`0755`). This prevents write failures on first upload for a new company or site.

### Processing Rules
1. **Max Dimensions:** The image is resized so that neither its width nor height exceeds **2560px**, while preserving the aspect ratio.
2. **Compression:** The image is compressed with subtle quality loss (targeting `~85%` quality for JPEG, `~75%` compression for PNG) to minimize file size without visible degradation.
3. **Format:** Image format is preserved (PNG stays PNG, JPG stays JPG). No format conversion at this stage.
4. **Accepted Formats:** **PNG** and **JPG** only. Any other format must be rejected before reaching the optimizer.
5. **In-Place Replacement:** The optimized image overwrites the original stored file at the same path. No additional copies are created.

### File Naming Convention
- Filenames must be a **unique hash** within the upload folder.
- Format: `{hash}-{site_id}-{company_id}.{ext}`.
- Example: `a3f9c2e1-42-7.jpg`.
- The hash component ensures uniqueness; the appended IDs aid in auditing and bulk cleanup.

### Module Integration Contract
Any module integrating with this service must:
1. Validate the file format (PNG/JPG) **before** upload persists.
2. Pre-calculate the full storage path and pass it to the service — directory creation is handled **by the service itself**, not by the module.

### Storage Path Examples per Module
- **Banners:** `{company_id}/{site_id}/banners/{filename}`

## Consequences
- **Positive:** Any future module (e.g., Posts, Authors) can reuse the service immediately without reinventing optimization logic.
- **Positive:** The Observer/Event pattern decouples the domain model from the optimization infrastructure.
- **Negative:** Requires discipline to always route image uploads through the naming convention, otherwise the auditing guarantees break down.

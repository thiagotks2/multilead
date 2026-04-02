# 16. Phone Number Validation and Formatting Strategy

Date: 2026-04-02  
Status: Approved

## Context
Phone numbers represent critical interaction nodes for the business (Lead reachability, CRM, WhatsApp API, Zimobi integrations). Historically, accepting raw user-supplied strings has led to fragmented databases where international, local, and improperly formatted entries collide. This fragmentation breaks search capabilities, crashes frontend text parsers, and causes expensive integration failures. We must ensure absolute determinism for phone data across the entire platform ecosystem regardless of the entity it attaches to.

## Decision
We will establish a **Centralized Phone Parsing Engine** using Domain-Driven Design principles. The `App\Support\Phone` class will serve as the single source of truth for handling numbers.

### 1. Mandatory Input Enforcement
**Rule:** It is strictly forbidden to input phone numbers into any entity within the system without validating it through the centralized engine. Every form submission, API payload, or CSV import must pass the `ValidPhone` Laravel Rule, which guarantees rejection of incompatible formats.

### 2. Format Definitions & Mathematical Logic
We define strict criteria to govern what constitutes acceptable data:

* **Brazilian Pattern (Strict Local Regex):**
  * Numbers evaluated as Brazilian (sizes 8, 9, 10, 11 natively or 12/13 with DDI) undergo strict local part validation.
  * If the local portion is **9 digits**, it MUST start with `9` (Mobiles).
  * If the local portion is **8 digits**, it MUST NOT start with `9` (Landlines).
  * Must be 12 or 13 digits where the first two digits are explicitly `55` (International scope declaring DDI Brazil).
* **International Pattern:**
  * Must be at least 8 digits long.
  * If the length is 12 digits or more, it MUST NOT start with `55` (otherwise, it falls under strict Brazilian validations).

### 3. State Preservation and Normalization (Database Storage)
Data persisting to the database will undergo a `toDatabase` normalization filter that aggressively strips formatting (removing dashes, spaces, and parenthesis) retaining pure integer character sets.
If the parser detects a Local or Regional Brazilian pattern, it will inherently attempt to append the default environment DDI (`55`) and DDD strings to guarantee that historical queries target a standard sequence.

### 4. Human Output Formatting (UI Display)
Storage serialization must never bleed into the User Interface natively. When extracting phone numbers for presentation:
* **Brazilian Numbers:** We mask them gracefully ensuring standard UX metrics: `+55 (DD) NNNNN-NNNN` (or NNNN-NNNN for fixed lines).
* **International Numbers:** We acknowledge their custom layout simply prepending the recognized DDI symbol: `+DDI [Remainder Block]`.

## Consequences

### Positive
- Cross-module stability natively guaranteed; third-party integrations (e.g. Zimobi) will rarely fail API calls due to dirty strings.
- Filament tables will exhibit high UI consistency globally using the dedicated `PhoneColumn`.
- Reduced cognitive load for developers building new entities (just import `PhoneInput::make()`).

### Negative
- Highly specialized or unusually short corporate international numbers might require edge-case bypasses if they drop below the 8-digit international heuristic threshold, requiring future whitelist logic implementation if requested by the stakeholders.

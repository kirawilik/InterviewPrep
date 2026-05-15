# Concepts Feature

## Goal

Allow users to create, manage, update, review, archive, restore, and track technical concepts inside their domains.

Each concept represents a technical topic the user wants to study for technical interviews.

Examples:

* Eloquent Relationships
* SOLID Principles
* REST API Authentication
* N+1 Problem

---

# Database Structure

Table: concepts

Fields:

* id
* domain_id (foreign key)
* title
* slug (optional, future SEO/API support)
* explanation (long text)
* difficulty (junior|mid|senior)
* status (to_review|in_progress|mastered)
* created_at
* updated_at
* deleted_at (soft deletes)

Indexes:

* domain_id
* status
* difficulty

---

# Relationships

* Domain hasMany Concepts
* Concept belongsTo Domain

Ownership:

* A Domain belongsTo User
* Therefore users only access concepts through their own domains

---

# Soft Deletes

Concepts use soft deletes.

Reasons:

* allow restoring deleted concepts
* preserve AI interview generation history
* prevent accidental permanent deletion

---

# Routes

GET     /domains/{domain}/concepts

GET     /domains/{domain}/concepts/create

POST    /domains/{domain}/concepts

GET     /domains/{domain}/concepts/{concept}

GET     /domains/{domain}/concepts/{concept}/edit

PUT     /domains/{domain}/concepts/{concept}

DELETE  /domains/{domain}/concepts/{concept}

PATCH   /domains/{domain}/concepts/{concept}/status

GET     /domains/{domain}/concepts/archived

PATCH   /concepts/{concept}/restore

DELETE  /concepts/{concept}/force-delete

---

# Filtering

Users can filter concepts by:

* status
* difficulty

Examples:

/domains/{domain}/concepts?status=mastered

/domains/{domain}/concepts?difficulty=senior

/domains/{domain}/concepts?status=mastered&difficulty=senior

Available status filters:

* to_review
* in_progress
* mastered

Available difficulty filters:

* junior
* mid
* senior

---

# Sorting

Users can sort concepts by:

* newest
* oldest
* alphabetical

---

# Pagination

Concept lists should be paginated.

Example:

* 10 concepts per page

---

# Controller

ConceptController

Methods:

* index
* create
* store
* show
* edit
* update
* destroy
* updateStatus
* archived
* restore
* forceDelete

---

# Controller Responsibilities

index:

* list concepts
* filter by status
* filter by difficulty
* sort results
* paginate results

updateStatus:

* update only concept status
* used for quick status changes directly from concept list

archived:

* show soft deleted concepts

restore:

* restore soft deleted concept

forceDelete:

* permanently delete concept

---

# Validation

Use Form Requests:

* StoreConceptRequest
* UpdateConceptRequest

Validation Rules:

title:

* required
* string
* max:255

explanation:

* required
* string

difficulty:

* required
* in:junior,mid,senior

status:

* required
* in:to_review,in_progress,mastered

---

# Default Values

Default status:

* to_review

Default difficulty:

* junior

---

# Display Labels

Difficulty:

* junior => Junior
* mid => Mid
* senior => Senior

Status:

* to_review => À revoir
* in_progress => En cours
* mastered => Maîtrisé

---

# Views

concepts/index.blade.php

concepts/create.blade.php

concepts/show.blade.php

concepts/edit.blade.php

concepts/archived.blade.php

---

# Empty State UX

If no concepts exist:

* show friendly empty message
* show "Create Concept" button

---

# Security

Users can only access their own concepts.

Concepts must belong to domains owned by the authenticated user.

Use:

* ConceptPolicy
* authorize()

Policy methods:

* view
* update
* delete
* restore
* forceDelete

Only the concept owner can:

* update concepts
* delete concepts
* restore concepts
* permanently delete concepts

---

# Future API Ready

Concepts may later be exposed through REST API resources.

Architecture should remain API-friendly.

---

# Notes

* Status can be updated quickly from concepts list
* Soft deleted concepts are not removed permanently
* AI interview generations are preserved even after soft delete
* Nested routes improve ownership security

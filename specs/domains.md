# Domains Feature

## Goal

Allow users to manage technical domains.

Examples:

* Laravel
* PHP OOP
* MySQL
* REST API

---

## User Stories

* User can create domain
* User can edit domain
* User can delete domain
* User can list domains

---

## Database

Table: domains

Fields:

* id
* user_id
* name
* color
* timestamps

---

## Relationships

* User has many domains
* Domain belongs to user

---

## Validation

name:

* required
* string
* max:255

color:

* required

---

## Pages

* domains/index
* domains/create
* domains/edit

---

## Security

Users can access only their own domains.

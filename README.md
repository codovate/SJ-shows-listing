# SJ-shows-listing

## Prerequisites

Before you begin, make sure you have the following installed locally:

- Composer  
- Docker  
- DDEV  
- Node.js (LTS recommended)  
- Gulp  
- direnv  

You should be comfortable using the terminal and running CLI-based tooling.

---

## Pre-setup (Important)

Before starting the project, you **must update the project naming** in the following files:

- `/.ddev/config.yaml`  

This ensures consistency across the local environment, theme assets, and tooling.

---

## Initial Environment Setup

Open a terminal in the project root and run:

`ddev config`

When prompted:

- Change the project name from the default to your chosen project name  
- Accept the remaining defaults unless you have a strong reason not to  

This step configures DDEV correctly for your local setup.

---

## Setting Up WordPress Locally

Once the environment is configured, run:

`ddev start`

On first run, this will:

- Start the Docker containers  
- Set up the local database  
- Generate a local `.env` file from `.env.example` using DDEV defaults  
- Install WordPress via Composer  
- Check out the Farlo starter theme  

Wait for this process to complete before proceeding.

---
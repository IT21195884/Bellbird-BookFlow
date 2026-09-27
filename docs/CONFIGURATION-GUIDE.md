# Bellbird BookFlow Configuration Guide

## Purpose

This document explains how the Bellbird BookFlow application
settings are managed.

## Configuration files

The `.env.example` file documents the required application
settings.

Each installation must create its own `.env` file.

The `.env` file must not be uploaded to GitHub because it may
contain private configuration information.

## Development settings

APP_ENV=development  
APP_DEBUG=true  
APP_URL=http://localhost:8000  
DB_PATH=database/bellbird.sqlite

## Production settings

APP_ENV=production  
APP_DEBUG=false  
APP_URL=the production HTTPS address  
DB_PATH=a protected persistent database location

## Database protection

The SQLite database contains application and customer data.

The database:

- must not be committed to GitHub;
- must not be stored inside the public web folder;
- must be backed up before deployment;
- must only be accessed by authorised staff.
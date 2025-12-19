# BackToGreen - Ecological Event Management System

## Overview
BackToGreen is a comprehensive platform for organizing and managing ecological events, waste collection, community groups, and environmental initiatives.

## Application Structure

### User Roles
- **MEMBRE (Member)**: Regular users who can participate in events, join groups, and schedule waste collections
- **ADMIN**: Administrators who have full access to backoffice features and management tools

### Core Features

#### 1. Event Management (`EvenementEcologique`)
**Frontend Routes:**
- `GET /events` - Browse all upcoming events
- `GET /events/{id}` - View event details
- `POST /events/{id}/register` - Register for an event
- `POST /events/{id}/cancel` - Cancel event participation
- `GET/POST /events/{id}/review` - Leave a review after attending

**Backend Routes (Admin):**
- `/evenement_ecologique` - Full CRUD management
- Create, edit, delete events
- View participants and statistics

**Features:**
- Events have categories (recycling, cleaning, tree_planting, education, conservation)
- Capacity management (max participants)
- Date/time scheduling
- Location tracking
- Reviews and ratings from participants

#### 2. User Participation (`Participation`)
- Users register for events
- Track participation status (INSCRIT, CONFIRME, ANNULE, COMPLETE)
- Participation history
- Ability to cancel before event starts
- Leave reviews after event completion

#### 3. Group Management (`Groupe` & `MembreGroupe`)
**Features:**
- Users can create groups for events (1-6 members)
- Groups are linked to specific events
- Track group membership status (ACTIF, INACTIF, EXPULSE)
- Group statistics and member management

**Routes:**
- `/groupe` - Group CRUD operations (backend)
- `/membre_groupe` - Member management
- `/membre_groupe/pending` - Pending join requests

#### 4. Waste Collection (`CollecteDechet` & `ZoneCollecte`)
**Collection Types:**
- PLASTIQUE (Plastic)
- VERRE (Glass)
- PAPIER (Paper)
- METAL (Metal)
- ORGANIQUE (Organic)

**Features:**
- Schedule waste collections
- Collection zones with GPS coordinates
- Zone capacity management
- Collection status tracking (PLANIFIEE, EN_COURS, TERMINEE, ANNULEE)

**Routes:**
- `/zone_collecte` - Manage collection zones
- `/collecte_dechet` - Manage collections
- Map view for zone locations

#### 5. Complaints System (`Reclamation`)
**Features:**
- Users can submit complaints/feedback
- Priority levels (HAUTE, MOYENNE, BASSE)
- Status tracking (OUVERTE, EN_COURS, RESOLUE, FERMEE)
- Admin responses
- Complaint history

**Routes:**
- `/reclamation` - Frontend and backend CRUD

#### 6. Review System (`Avis`)
- Rate events (1-5 stars)
- Leave comments
- View average ratings
- Only available after event completion

### Navigation Structure

#### Public Pages
- `/` - Homepage with site overview
- `/login` - User login
- `/register` - User registration
- `/forgot-password` - Password reset request
- `/reset-password/{token}` - Password reset form

#### Authenticated User Pages
- `/profile` - User dashboard
  - View participations
  - View created groups
  - View reclamations
  - View collection history
  - Statistics overview

- `/events` - Browse and register for events
- `/events/{id}` - Event details and registration
- `/profile/edit` - Edit profile information

#### Admin Pages (`/admin` prefix)
- `/admin` - Admin dashboard
- `/dashboard` - Statistics and analytics
- `/user` - User management
- `/evenement_ecologique` - Event management
- `/participation` - Participation management
- `/groupe` - Group management
- `/membre_groupe` - Group membership management
- `/zone_collecte` - Collection zone management
- `/collecte_dechet` - Waste collection management
- `/reclamation` - Complaint management
- `/avis` - Review management

### Database Relationships

```
User (Hub Entity)
├── has many → CollecteDechet (waste collections)
├── has many → Reclamation (complaints)
├── has many → Avis (reviews)
├── has many → Participation (event participations)
├── has many → MembreGroupe (group memberships)
└── has many → Groupe (created groups)

EvenementEcologique (Events)
├── belongs to → User (organizer)
├── has many → Participation
├── has many → Avis
└── has many → Groupe

Groupe (Groups)
├── belongs to → User (creator)
├── belongs to → EvenementEcologique
└── has many → MembreGroupe

CollecteDechet (Waste Collections)
├── belongs to → User
└── belongs to → ZoneCollecte

ZoneCollecte (Collection Zones)
└── has many → CollecteDechet
```

### Key User Flows

#### 1. New User Journey
1. Visit homepage → Click "Register"
2. Fill registration form → Verify email
3. Login → Complete profile
4. Browse events → Register for event
5. Attend event → Leave review

#### 2. Event Participation
1. Browse `/events`
2. Click event → View details
3. Check availability and date
4. Click "Register" → Confirm participation
5. Receive confirmation (flash message)
6. View participation in `/profile`
7. Cancel if needed (before event starts)
8. After event → Leave review

#### 3. Group Creation
1. Admin creates event
2. Users register for event
3. Users can create/join groups for the event
4. Group capacity: 1-6 members
5. Track group activities

#### 4. Waste Collection
1. Browse collection zones (map view)
2. Schedule collection
3. Select waste type and quantity
4. Track collection status
5. View collection history in profile

#### 5. Submit Complaint
1. Go to reclamation section
2. Fill complaint form
3. Set priority
4. Track status
5. Receive admin response

### Security Features
- Password hashing with Symfony's password hasher
- CSRF protection on forms
- Role-based access control
- Email verification
- OAuth integration (Google)
- Secure password reset with tokens

### Additional Features
- Multi-language support (translation system)
- QR code generation
- PDF export for participations
- Email notifications
- SMS integration
- Map integration for zones
- Statistics dashboard
- Data validation
- Pagination

## Getting Started

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Symfony CLI (optional)

### Installation
1. Clone repository
2. Run `composer install`
3. Configure `.env` with database credentials
4. Run `php bin/console doctrine:migrations:migrate`
5. Start server: `php -S localhost:8000 -t public`
6. Visit `http://localhost:8000`

### First Admin User
Create admin user manually in database or via console command with role `ADMIN`.

## File Structure
```
src/
├── Controller/         # All route handlers
├── Entity/            # Database models
├── Form/              # Form types
├── Repository/        # Database queries
├── Service/           # Business logic
└── Security/          # Authentication

templates/
├── frontoffice/       # Public user interface
├── backoffice/        # Admin interface
├── front/             # Frontend pages
└── auth/              # Login/register pages
```

## Contributing
This is an educational project for managing ecological events and promoting environmental awareness.

## License
Proprietary - BackToGreen Team 2024

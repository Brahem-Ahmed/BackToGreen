# BackToGreen Application Analysis & Connectivity Map

## Data Model Overview

### Core Entities & Relationships

1. **User** (Hub Entity)
   - Has many: CollecteDechet, Reclamation, Avis, MembreGroupe
   - Can be: Organizer of EvenementEcologique
   - Role: ADMIN or MEMBRE (RoleUser enum)

2. **EvenementEcologique** (Events)
   - Belongs to: User (organizer)
   - Has many: Participation, Avis, Groupe
   - Categories: recycling, cleaning, tree_planting, education, conservation, other

3. **Participation** (User participation in events)
   - Belongs to: User, EvenementEcologique
   - Status: INSCRIT, CONFIRME, ANNULE, COMPLETE

4. **Groupe** (Groups for events)
   - Belongs to: User (creator), EvenementEcologique
   - Has many: MembreGroupe
   - Capacity: 1-6 members

5. **MembreGroupe** (Group membership)
   - Belongs to: User, Groupe, EvenementEcologique
   - Status: ACTIF, INACTIF, EXPULSE

6. **CollecteDechet** (Waste collection)
   - Belongs to: User, ZoneCollecte
   - Type: PLASTIQUE, VERRE, PAPIER, METAL, ORGANIQUE
   - Status: PLANIFIEE, EN_COURS, TERMINEE, ANNULEE

7. **ZoneCollecte** (Collection zones)
   - Has many: CollecteDechet
   - Has geolocation (latitude, longitude)

8. **Reclamation** (Complaints)
   - Belongs to: User
   - Priority: HAUTE, MOYENNE, BASSE
   - Status: OUVERTE, EN_COURS, RESOLUE, FERMEE

9. **Avis** (Reviews for events)
   - Belongs to: User, EvenementEcologique
   - Rating: 1-5 stars

## Controller Structure

### Frontend Controllers
- **HomeController**: Landing page (/)
- **FrontController**: Event listing (/evenements)
- **Front/ProfileController**: User profile
- **Front/FrontParticipationController**: User participation management
- **Front/MapController**: Map views

### Backend (Admin) Controllers  
- **BackOfficeController**: Admin dashboard (/admin)
- **DashboardController**: Statistics dashboard (/dashboard)
- **UserController**: User management (/user)
- **EvenementEcologiqueController**: Event management
- **ParticipationController**: Participation management
- **GroupeController**: Group management
- **MembreGroupeController**: Group membership management
- **CollecteDechetController**: Waste collection management
- **ZoneCollecteController**: Collection zone management (/zone_collecte)
- **ReclamationController**: Complaint management (/reclamation)
- **AvisController**: Review management

### Authentication Controllers
- **SecurityController**: Login, logout, password reset
- **RegistrationController**: User registration (/register)
- **OAuthController**: Google OAuth

## Issues Found & Fixes Needed

### 1. Missing Event Routes in Frontend
- Need front event show route
- Need front event registration route

### 2. Navigation Issues
- Frontoffice navbar has dead links (#campaigns, #events, #about)
- Should link to actual routes

### 3. Missing User Relationship
- User needs MembreGroupe relationship (inversed by membreGroupes)
- User needs Groupe relationship for created groups

### 4. Profile Integration
- Profile should show user's events, participations, groups

### 5. Missing Map Integration  
- ZoneCollecte has coordinates but needs map display
- Events could use location mapping

## Recommended Application Flow

### Public User Journey
1. Homepage (/) → Browse events → Login/Register
2. Register → Email verification → Profile setup
3. Browse Events → View event details → Register for event
4. View Collection Zones (Map) → Schedule waste collection
5. Submit complaints → Track status
6. Leave reviews for attended events

### Logged-in User Journey
1. Dashboard showing: My Events, My Groups, My Collections
2. Join/Create groups for events
3. View participation history
4. Manage profile
5. Track complaints

### Admin Journey
1. Admin dashboard with statistics
2. Manage all users, events, groups
3. Handle complaints
4. Manage collection zones
5. View analytics

## Next Steps
1. Fix User entity relationships
2. Create missing frontend routes
3. Update navigation links
4. Implement proper access control
5. Connect all features through proper routing

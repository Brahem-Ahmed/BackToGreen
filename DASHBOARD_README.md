# BackToGreen User Dashboard

## Overview
A beautiful, feature-rich user dashboard for the BackToGreen environmental platform. When users log in, they are redirected to a comprehensive dashboard that displays all their environmental activities and impact.

## Features Implemented

### 1. **User Dashboard** (`/profile`)
After login, users are redirected to their personal dashboard which includes:

#### Statistics Cards
- **Event Participations**: Total number of events the user has registered for
- **Groups Joined**: Number of environmental groups the user is part of
- **Waste Collections**: Number of waste collection activities
- **Event Reviews**: Number of reviews/feedback submitted

#### Main Sections

**Upcoming Events**
- Table view of all registered events
- Shows event title, date, time, location, and registration status
- Direct links to event details
- Status badges (Registered, Confirmed, Cancelled)
- Categories displayed as tags

**Quick Actions Panel**
- Join Event - Browse and register for new events
- Submit Complaint - Report environmental issues
- Schedule Collection - Arrange waste collection

**My Complaints**
- Recent complaints submitted by the user
- Status tracking (Open, In Progress, Resolved, Closed)
- Quick overview with creation dates

**My Groups**
- Grid view of environmental groups
- Shows group name, description, member count
- Creation date for each group

**Recent Collections**
- Table of waste collection activities
- Type icons (Plastic, Glass, Paper, Metal, Organic)
- Quantity in kg
- Status tracking (Planned, In Progress, Completed, Cancelled)

### 2. **Profile Edit** (`/profile/edit`)
- Update personal information (name, email, phone, address)
- Change password (optional)
- Clean, user-friendly form with validation
- Success/error flash messages
- Back to dashboard navigation

### 3. **Enhanced Navigation**
- Dynamic navbar that shows:
  - For logged-in users: Home, Events, Dashboard
  - For guests: Home, Events, Campaigns, Impact, About
- User dropdown menu with:
  - Profile/Dashboard link
  - Admin dashboard (for admin users)
  - Logout option

### 4. **Login Redirect**
- After successful login, users are automatically redirected to `/profile` (their dashboard)
- Provides immediate overview of their environmental impact

## Technical Implementation

### Routes
```php
/profile          → User Dashboard (ProfileController::index)
/profile/edit     → Edit Profile (ProfileController::edit)
```

### Security
- Dashboard requires `ROLE_USER` authentication
- Login redirect configured in `security.yaml`:
  ```yaml
  default_target_path: app_profile
  ```

### Templates
- `templates/front/dashboard/index.html.twig` - Main dashboard
- `templates/front/profile/edit.html.twig` - Profile editor
- `templates/front/layout.html.twig` - Layout wrapper
- Updated navbar with conditional links

### Data Flow
The ProfileController gathers data from multiple repositories:
- ParticipationRepository - User's event registrations
- GroupeRepository - User's groups
- ReclamationRepository - User's complaints
- CollecteDechetRepository - User's waste collections
- User entity relations - Reviews (Avis)

## Design Highlights

### UX Features
- **Clean Dashboard Layout**: Card-based design with shadow effects
- **Color-Coded Statistics**: Each stat card has a unique color (primary, success, info, warning)
- **Large Icons**: Font Awesome icons with opacity for visual hierarchy
- **Responsive Grid**: Bootstrap 5 responsive columns
- **Empty States**: Helpful messages and CTAs when no data exists
- **Badge System**: Visual status indicators for events, complaints, collections
- **Quick Actions**: Always-accessible buttons for common tasks
- **Hover Effects**: Cards have hover shadow animations
- **Icon System**: Contextual icons for waste types, dates, locations

### Visual Theme
- Primary green color: `#198754` (environmental theme)
- Clean white cards with subtle shadows
- Consistent spacing and padding
- Professional typography with clear hierarchy
- Bootstrap 5 components for consistency

## Empty State Handling
The dashboard gracefully handles users with no data:
- Empty event list shows "Browse Events" CTA
- Empty groups shows encouragement to join/create
- Empty collections shows "Schedule Collection" button
- Empty complaints shows success icon (no issues!)

## Next Steps
To test the dashboard:
1. Create a test user via `/register`
2. Login with the test user
3. You'll be redirected to `/profile` automatically
4. Explore different sections
5. Use Quick Actions to create sample data
6. Watch the statistics update as you participate

## Admin vs User Experience
- **Regular Users**: See their personal dashboard at `/profile`
- **Admin Users**: Can access both `/profile` (personal) and `/admin` (backoffice)
- Navbar dynamically shows admin dashboard link for admin users

---

**Built with**: Symfony 6.4, Doctrine ORM, Bootstrap 5, Font Awesome 6
**Focus**: Clean UX, comprehensive feature display, easy navigation

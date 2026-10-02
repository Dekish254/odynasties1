# Odynasties — XAMPP Full-Stack Version

## New access model

### Public landing page
Visitors see only Odynasties promotional information and the login form. Community content is available after authentication.

The landing-page login requires the person to choose:
- Member
- Administrator

The system checks both the credentials and the stored role. A member cannot sign in through the administrator option, and an administrator cannot sign in through the member option.

If a person chooses Member and no account exists, the page directs them to Member Sign Up. Administrator accounts cannot be self-created; an existing administrator creates or promotes them.

### Member
Members can:
- View community information after login
- View members, events and news
- Donate/register for blood donation
- Submit and view support requests
- Give testimonies where provided by the site
- Update their own profile
- Upload/change their profile picture
- Change their own password

Members cannot access administrator URLs or administrator functions.

### Administrator
Administrators can:
- Manage members
- View currently active member sessions
- Log members out
- Reset member passwords
- Manage donations, events, news and support requests
- Add administrators
- Promote an existing member to administrator
- Remove administrator privileges from another administrator
- View Administrator Reports

Each successful administrator login creates its own database login session. Multiple administrators can be logged in at the same time on different devices/browsers.

### Administrator Reports
The system records administrator actions with:
- Administrator name/id
- Action
- Basic action details
- Date/time
- IP address

Do not store passwords or password values in reports.

## XAMPP setup

1. Extract the project to `C:\xampp\htdocs\odynasties`.
2. Start Apache and MySQL in XAMPP.
3. Open `http://localhost/phpmyadmin/`.
4. Import `database/odynasties.sql`.
5. Open `http://localhost/odynasties/`.
6. Create the first administrator using `create_admin.php`, then remove/disable that bootstrap file after use. Existing administrators can create/promote additional administrators from the Admin Dashboard.

Database settings are in `config/config.php`.


## Important: Member and Administrator sessions are separate

The site uses two separate PHP session cookies:
- `ODY_MEMBER_SESSION` for members
- `ODY_ADMIN_SESSION` for administrators

This means a member can remain logged in while an administrator logs in from the same browser. A member page continues to use the member session, while `/admin/` uses the administrator session. Logging out one role does not destroy the other role's session.

Do not use `active_role` to decide permissions. Authorization is checked by the role-specific session and the database role.

## Navigation by role

When an administrator is authenticated, the main site header is replaced by the administrator navigation:
Dashboard, Members, Online Users, Administrators, Reports, Support Requests, Donations, Events, News, Messages, and Logout.

When a member is authenticated, the member navigation is shown instead. A member never receives administrator navigation links.

## Member landing page
After a successful member login, the member lands on `member-home.php`, a community-focused page with promotional cards for blood donation, project support, community support requests, events, news and member connection. The profile editor is not shown on the landing page; members open **My Profile** from the navigation only when they want to update their personal details.

## Member home online list

The member landing page shows a "Members Online" section with the profile picture and name of active members. A member is considered online when they have an active member login session with activity within the last 5 minutes. Administrator sessions are intentionally excluded from this member-facing list.

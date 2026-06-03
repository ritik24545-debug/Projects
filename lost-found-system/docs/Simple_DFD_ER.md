## Lost & Found System — Simple DFD (Level 0–2) and ER Diagram

Legend: EE = External Entity, P# = Process, DS# = Data Store, DF-xx = Data Flow

Basic DFD components used here:
- External Entity: rectangle (User/Admin)
- Process: rounded rectangle (system functions like User, Reporting, Matching)
- Data Store: parallel lines/cylinder (Users, Lost, Found, Matches)
- Data Flow: arrows with DF-xx labels

### Level 0 (Context Diagram)

```
EE User ──DF-01/05/07──▶ Lost & Found System ◀──DF-19── EE Admin
     ▲                           │
     │                           └──DF-17/18──▶ Notifications (Email/SMS, Dashboard)
     └──DF-04 Session/Status
```

What it shows: Who interacts with the system and at a high level what flows happen.

---

### Level 1 (Top-Level Processes)

Processes: P1 User, P2 Item Reporting, P3 Matching, P4 Admin Review, P5 Notifications
Data Stores: DS1 Users, DS2 Lost, DS3 Found, DS4 Matches

```
EE User ──DF-01/03──▶ P1 User ──DF-02──▶ DS1 Users ──DF-04──▶ EE User

EE User ──DF-05/07──▶ P2 Item Reporting ──DF-06/08──▶ DS2 Lost / DS3 Found ──DF-09──▶ EE User

DS2 Lost ──DF-10──▶ P3 Matching ◀──DF-11── DS3 Found ──DF-12──▶ DS4 Matches (Pending)

DS4 Matches (Pending) ──DF-13──▶ P4 Admin Review ◀──DF-19── EE Admin ──DF-14──▶ DS4 (Accepted/Rejected)

DS4 Status ──DF-16──▶ P5 Notifications ──DF-17/18──▶ Email/SMS + Dashboard
```

---

### Level 2 (Selected Details)

P1: User Management
- DF-01 Register / DF-03 Login → Validate → DF-02 Store DS1 → DF-04 Session

P2: Item Reporting
- DF-05 Lost Form → Validate → DF-06 Store DS2 → DF-09 Ack
- DF-07 Found Form → Validate → DF-08 Store DS3 → DF-09 Ack

P3: Matching Engine
- DF-10/11 Read DS2/DS3 → Compare (name, desc, date, location) → DF-12 Write DS4(Pending)

P4: Admin Review
- DF-13 Load DS4(Pending) → Verify → DF-14 Accept/Reject → Update DS4

P5: Notifications
- DF-16 On status change in DS4 → DF-17/18 Notify users (Email/SMS & Dashboard)

---

### Simple ER Diagram (Text)

```
Users (id, fullname, email, username, phone, aadhar, password, created_at)
Lost_Items (id, user_id → Users.id, item_name, description, date_lost, location, created_at)
Found_Items (id, user_id → Users.id, item_name, description, date_found, location, created_at)
Matched_Items (id, lost_item_id → Lost_Items.id, found_item_id → Found_Items.id, status, matched_on)
```

Relationships:
- One User → many Lost_Items, many Found_Items
- Lost_Items and Found_Items → many-to-many via Matched_Items

---

Tip: To export images (PNG/PDF), use the Mermaid `.mmd` files and `render-mermaid.ps1` in `docs/`.



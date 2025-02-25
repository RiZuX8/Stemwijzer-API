## Statement
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /statements | Haal alle statements op. |
| GET    | /statements/{id} | Haal een specifiek statement op. |
| POST   | /statements | Voeg een nieuw statement toe. |
| PUT    | /statements/{id} | Werk een bestaand statement bij. |
| DELETE | /statements/{id} | Verwijder een statement. |

## Party
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /parties | Haal alle partijen op. |
| GET    | /parties/{id} | Haal een specifieke partij op. |
| POST   | /parties | Voeg een nieuwe partij toe. |
| PUT    | /parties/{id} | Werk een bestaande partij bij. |
| DELETE | /parties/{id} | Verwijder een partij. |

## PartyStatement
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /party-statements | Haal alle partij-uitspraken op. |
| GET    | /party-statements/party/{partyID} | Haal uitspraken van een specifieke partij op. |
| GET    | /party-statements/statement/{statementID} | Haal uitspraken van een specifiek statement op. |
| POST   | /party-statements | Voeg een nieuwe partij-uitspraak toe. |
| PUT    | /party-statements | Werk een bestaande partij-uitspraak bij. |
| DELETE | /party-statements | Verwijder een partij-uitspraak. |

## Admin
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /admins | Haal alle admins op. |
| GET    | /admins/id/{id} | Haal een specifieke admin op via id. |
| GET    | /admins/email/{email} | Haal een specifieke admin op via email. |
| POST   | /admins | Voeg een nieuwe admin toe. |
| POST   | /admins/login | Log in als admin. |
| PUT    | /admins/{id} | Werk een bestaande admin bij. |
| DELETE | /admins/{id} | Verwijder een admin. |

## SuperAdmin
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /superadmins | Haal alle superadmins op. |
| GET    | /superadmins/id/{id} | Haal een specifieke superadmin op via id. |
| GET    | /superadmins/email/{email} | Haal een specifieke superadmin op via email. |
| POST   | /superadmins | Voeg een nieuwe superadmin toe. |
| POST   | /superadmins/login | Log in als superadmin. |
| PUT    | /superadmins/{id} | Werk een bestaande superadmin bij. |
| DELETE | /superadmins/{id} | Verwijder een superadmin. |
# ETRANSPORT Module

This module provides integration with ANAF's ETRANSPORT API for managing transport declarations in Romania.

## Features

- **UPLOAD**: Submit transport declarations (versions 1 and 2)
- **LISTA**: List transport declarations with filtering by days
- **STARE**: Check the status of a submitted declaration
- **INFO**: Get transport information for transporters

## API Endpoints

### Upload Transport Declaration
```
POST /api/etransport/upload
```
**Parameters:**
- `cif` (required): Company fiscal code (max 13 digits)
- `xml_content` (required): XML content of the transport declaration
- `version` (optional): API version (1 or 2, default: 1)

### List Transport Declarations
```
GET /api/etransport/lista?cif={cif}&zile={days}
```
**Parameters:**
- `cif` (required): Company fiscal code
- `zile` (required): Number of days to query (1-60)

### Check Declaration Status
```
GET /api/etransport/stare?id_incarcare={id}&cif={cif}
```
**Parameters:**
- `id_incarcare` (required): Upload ID returned from upload operation
- `cif` (required): Company fiscal code

### Get Transport Information
```
GET /api/etransport/info?cui_op={organizer_cif}&cui_decl={declarer_cif}&uit={uit}&ref_decl={reference}
```
**Parameters:**
- `cui_op` (required): Organizer fiscal code
- `cui_decl` (optional): Declarer fiscal code
- `uit` (optional): UIT identifier
- `ref_decl` (optional): Declarer reference

## Token Management

ETRANSPORT uses OAuth2 authentication. Tokens are managed through the `/api/etransporttokens` endpoints:

- `GET /api/etransporttokens` - List tokens
- `POST /api/etransporttokens/store` - Create token
- `POST /api/etransporttokens/edit/{id}` - Update token
- `POST /api/etransporttokens/delete/{id}` - Delete token
- `GET /api/etransporttokens/export` - Export tokens
- `POST /api/etransporttokens/import` - Import tokens

## Environment Configuration

Add these variables to your `.env` file:

```env
# ETRANSPORT Configuration
ETRANSPORT_PROD_BASE_URL=https://api.anaf.ro/prod/ETRANSPORT/ws/v1
ETRANSPORT_TEST_BASE_URL=https://api.anaf.ro/test/ETRANSPORT/ws/v1
ETRANSPORT_CERT_PROD_BASE_URL=https://webserviceapl.anaf.ro/prod/ETRANSPORT/ws/v1
ETRANSPORT_CERT_TEST_BASE_URL=https://webserviceapl.anaf.ro/test/ETRANSPORT/ws/v1
```

## Authentication

The module uses OAuth2 with the same credentials as e-invoicing:

```env
CLIENT_ANAF_ID=your_client_id
CLIENT_ANAF_SECRET=your_client_secret
```

## Permissions

Required permissions for API access:
- `uploadEtransport`: Upload declarations
- `viewEtransport`: View declarations and status
- `viewEtransporttokens`: Manage tokens
- `addEtransporttokens`: Add tokens
- `editEtransporttokens`: Edit tokens
- `deleteEtransporttokens`: Delete tokens
- `exportEtransporttokens`: Export tokens
- `importEtransporttokens`: Import tokens

## Error Handling

All endpoints return JSON responses with appropriate HTTP status codes:
- `200`: Success
- `400`: Bad request (missing parameters)
- `401`: Unauthorized (invalid token)
- `403`: Forbidden (insufficient permissions)
- `500`: Internal server error

Error responses include an `error` field with details.

## Logging

All API calls are logged with relevant information for debugging and monitoring.
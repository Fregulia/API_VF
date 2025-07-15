# Exemplos de Uso da API

## Autenticação

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer {seu-token}"
```

### Logout de todos os dispositivos
```bash
curl -X POST http://localhost:8000/api/logout-all \
  -H "Authorization: Bearer {seu-token}"
```

### Revogar token específico
```bash
curl -X POST http://localhost:8000/api/revoke-token \
  -H "Authorization: Bearer {seu-token}" \
  -H "Content-Type: application/json" \
  -d '{"revoke_all": false}'
```

## Usuários (Protegido)

### Listar usuários
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer {seu-token}"
```

### Criar usuário
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer {seu-token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Novo Usuario","email":"novo@example.com","password":"password123","role":"user"}'
```

## Esportes

### Listar esportes (público)
```bash
curl -X GET http://localhost:8000/api/esporte
```

### Criar esporte (apenas admin)
```bash
curl -X POST http://localhost:8000/api/esporte \
  -H "Authorization: Bearer {token-admin}" \
  -H "Content-Type: application/json" \
  -d '{"nome":"Futebol","federacao":"CBF","descricao":"Esporte mais popular do Brasil"}'
```

## Atletas

### Listar atletas (público)
```bash
curl -X GET http://localhost:8000/api/atleta
```

### Criar atleta (admin/manager)
```bash
curl -X POST http://localhost:8000/api/atleta \
  -H "Authorization: Bearer {token-admin-ou-manager}" \
  -H "Content-Type: application/json" \
  -d '{"nome":"João Silva","idade":25,"categoria":"Profissional","esporte_id":1}'
```

## Treinadores

### Listar treinadores (público)
```bash
curl -X GET http://localhost:8000/api/treinador
```

### Criar treinador (admin/manager)
```bash
curl -X POST http://localhost:8000/api/treinador \
  -H "Authorization: Bearer {token-admin-ou-manager}" \
  -H "Content-Type: application/json" \
  -d '{"nome":"Carlos Pereira","cref":"123456-G/SP","especialidade":"Preparação Física","esporte_id":1}'
```

## Usuários de Teste Criados

- **Admin**: admin@example.com / password
- **Manager**: manager@example.com / password  
- **User**: user@example.com / password

## Estrutura de Permissões

- **GET** (esportes, atletas, treinadores): Público
- **POST/PUT/DELETE** (esportes): Apenas admin
- **POST/PUT/DELETE** (atletas, treinadores): Admin e manager
- **Usuários**: Cada usuário pode ver/editar apenas seus próprios dados (exceto admin)
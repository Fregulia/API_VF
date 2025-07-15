# Instruções de Deploy - Heroku

## Pré-requisitos
```bash
# Instalar Heroku CLI
# Windows: https://devcenter.heroku.com/articles/heroku-cli
# Login no Heroku
heroku login
```

## Deploy Inicial
```bash
# 1. Criar aplicação no Heroku
heroku create laravel-api-esportiva

# 2. Adicionar PostgreSQL
heroku addons:create heroku-postgresql:mini

# 3. Configurar variáveis de ambiente
heroku config:set APP_KEY=$(php artisan key:generate --show)
heroku config:set APP_ENV=production
heroku config:set APP_DEBUG=false

# 4. Deploy
git add .
git commit -m "Initial deploy"
git push heroku main

# 5. Executar migrações
heroku run php artisan migrate --force
heroku run php artisan db:seed --force
```

## Deploy Automático via GitHub
1. Configure secrets no GitHub:
   - `HEROKU_API_KEY`: Sua API key do Heroku
   - `HEROKU_APP_NAME`: laravel-api-esportiva

2. Push para branch main executa deploy automático

## Verificar Deploy
```bash
heroku logs --tail
heroku open
```

## URL da API
- Produção: https://laravel-api-esportiva.herokuapp.com
- Endpoints: /api/esporte, /api/atleta, /api/treinador
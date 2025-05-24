# Mini ERP

*Pequeno sistema ERP para gerenciamento de Produtos, Cupons, Pedidos e Estoque, desenvolvido em Laravel 12.*

---

## 📦 Tecnologias

* **Framework:** Laravel 12
* **Banco de dados:** MySQL / MariaDB
* **Front-end:** Bootstrap 5
* **Envio de e-mails:** SMTP (Mailtrap) + Mailable / Queue
* **Filas (opcional):** database

---

## 🚀 Pré-requisitos

* PHP >= ^8.2
* Composer
* MySQL
* Node.js (opcional, para assets)

---

## 🛠️ Instalação e configuração

1. **Clone o repositório**

   ```bash
   git clone https://github.com/enzopatriarca/mini-erp.git
   cd mini-erp
   ```

2. **Instale dependências PHP**

   ```bash
   composer install
   ```

3. **Configure o ambiente**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Ajuste o `.env`**

   ```dotenv
   APP_NAME="MiniERP"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mini_erp
   DB_USERNAME=root
   DB_PASSWORD=

   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=SEU_USUARIO_MAILTRAP
   MAIL_PASSWORD=SUA_SENHA_MAILTRAP
   MAIL_ENCRYPTION=null
   MAIL_FROM_ADDRESS=hello@example.com
   MAIL_FROM_NAME="${APP_NAME}"

   # Se for usar filas:
   QUEUE_CONNECTION=database
   ```

5. **Migre o banco**

   ```bash
   php artisan migrate
   ```

6. **(Opcional) Inicie worker de filas**

   ```bash
   php artisan queue:work
   ```

7. **Rode o servidor**

   ```bash
   php artisan serve
   ```

---

## ⚙️ Funcionalidades

### Produtos

* CRUD completo
* Cadastro de variações e controle de estoque

### Carrinho

* Armazenado em sessão
* Valida quantidade vs. estoque
* Cálculo de frete:

  * Subtotal entre R\$ 52,00 e R\$ 166,59 → R\$ 15,00
  * Subtotal > R\$ 200,00 → grátis
  * Demais valores → R\$ 20,00

### Cupons

* CRUD de cupons
* Validade e valor mínimo de subtotal

### Pedido

* Ao finalizar:

  1. Persiste itens, subtotal, frete, total e endereço
  2. Decrementa estoque
  3. Envia e-mail de confirmação

### Webhook de Atualização de Status

**Endpoint**  
`POST /api/webhook/status`

**Headers**  

Content-Type: application/json

**Payload**  
```json
{
  "id": 123,
  "status": "aprovado"
}

curl -X POST http://seu-host/api/webhook/status \
     -H "Content-Type: application/json" \
     -d '{"id": 123, "status": "aprovado"}'

## 📧 Testando e-mails

1. Configure Mailtrap no `.env` (veja seção acima).
2. Execute uma finalização de pedido: você verá o envio (ou o log “E-mail enviado…”).
3. Acesse a *Sandbox Inbox* do Mailtrap para verificar o HTML gerado.

Se quiser rodar de forma assíncrona, basta descomentar em `PedidoController@finalizar`:

```php
Mail::to($request->email)
    // ->send(new PedidoConfirmado($pedido));
    ->queue(new PedidoConfirmado($pedido));
```

e manter o **queue worker** ativo.

---

> **Dica:** ao subir em servidor de produção, ajuste `QUEUE_CONNECTION=redis` (ou outro driver) e rode `supervisor` para gerenciar workers.

---

© 2025 Enzo Patriarca

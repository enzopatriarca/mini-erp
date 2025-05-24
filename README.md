Mini ERP (Enzo Patriarca)

Pequeno sistema ERP para gerenciamento de Produtos, Cupons, Pedidos e Estoque, desenvolvido em Laravel 12.

📦 Tecnologias

Framework: Laravel 12

Banco de dados: MySQL/MariaDB

Front-end: Bootstrap 5

Envio de e-mails: SMTP (Mailtrap) + Mailable/Queue

Filas (opcional): database

🚀 Pré-requisitos

PHP >= 8.1

Composer

MySQL

Node.js (opcional, se for usar assets)

🛠️ Instalação e configuração

1.Clone este repositório:
    git clone https://github.com/enzopatriarca/mini-erp.git
    cd mini-erp
2.Instale as dependências PHP:
    composer install
3.Copie o arquivo de ambiente e gere a chave da aplicação:
    cp .env.example .env
    php artisan key:generate
4.onfigure suas credenciais no .env:
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
    MAIL_USERNAME=seu_usuario_mailtrap
    MAIL_PASSWORD=sua_senha_mailtrap
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=hello@example.com
    MAIL_FROM_NAME="${APP_NAME}"

    # Para usar filas via banco de dados:
    QUEUE_CONNECTION=database
5.Crie as tabelas no banco:
    php artisan migrate
6.Inicie o servidor de desenvolvimento:
    php artisan serve

Funcionamento

Produtos: CRUD de produtos, cadastro de variações e controle de estoque.

Carrinho: Guardado em sessão; controla quantidade vs. estoque e calcula frete conforme regras:

Subtotal entre R$52,00 e R$166,59: frete R$15,00

Subtotal > R$200,00: frete grátis

Outros valores: frete R$20,00

Cupons: CRUD de cupons com validade e mínimo de subtotal.

Pedido: Ao finalizar, grava itens, decrementa estoque e envia e-mail de confirmação.

E-mail: Template Markdown em resources/views/emails/pedido_confirmado.blade.php. Para usar assincronamente, descomente ->queue() no controller e rode workers.

Webhook: Rota POST /webhook/status que recebe { id, status }. Se cancelado, devolve estoque e remove pedido; se aprovado, decrementa apenas uma vez.
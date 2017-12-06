DB1 - Pedido de Vendas
Avaliação técnica PHP/Symfony

Video demonstração: https://drive.google.com/file/d/1nM5HkAKyGVwOXdr1xBG7WZ900v77SAEj/view?usp=sharing

1 - Copiar Repositorio.
2 - Ajustar as configurações do banco de dados em app/config/parameters
3 - Executar o comando: app/console doctrine:cache:clear
4 - Criar Schema: bin/console doctrine:database:create
5 - Atualizar Schema: bin/console doctrine:schema:update --force
6 - bin/console serve:run
7 - Abrir no navegador localhost:8000

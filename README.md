# BraPedia ![Logo BraPedia](https://raw.githubusercontent.com/Nilton-hub/BraPedia/0d1a290e97a20430fc02c92075a25450c8c60269/public/assets/images/logo.svg)
Uma aplicação web destinada a criação de perfil para publicação e gerenciamento de artigos onde os usuários autenticados poderão ler e comentar os artigos 
uns dos outros e responder aos comentarios uns dos outros.

## Como Rodar a Aplicação Corretamente

1. Você deve renomear o arquivo `.env.exemple` na pasta raíz do projeto para `.env` ou fazer uma cópia dele e renomear 
para `.env`;
2. substituir todos os valores (ou pelo menos nas variáveis de conexão ao banco de dados) das variáveis de ambientes lá
dentro para variáveis corretas, pois elas estão com valores demonstrativos que não funcionam;
3. executar o arquivo em `data/database/main_database.sql`. Ele criará o banco de dados chamado **brapedia** e todas as 
tabelas nescessárias para o funcionamento do sistema;
4. abrir o arquivo `src/boot/config.php` e lá dentro, definir a constante CONF_BASE_URL para o domínio correto em 
localhost. Por exemplo `const CONF_BASE_URL = 'http://localhost:8888'`;

Estes passos permitem que ao iniciar um servidor web capaz de executar o PHP e acessar a respectiva URL pelo navegador, 
você consiga ver a aplicação executando.

### Contato do Desenvolvedor

- Email: [joseniltonduarte3@gmail.com](mailto:joseniltonduarte3@gmail.com)
- [Linkedin](https://www.linkedin.com/in/nilton-duarte-05b530175/)
- [Instagram](https://www.instagram.com/duarte_2000/)
- [Twitter](https://twitter.com/NiltonD17284468)

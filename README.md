# [BraPedia](http://brapedia.infinityfreeapp.com/) ![Logo BraPedia](https://raw.githubusercontent.com/Nilton-hub/BraPedia/0d1a290e97a20430fc02c92075a25450c8c60269/public/assets/images/logo.svg)

Uma aplicação web destinada a criação de perfil onde os usuários poderão interagir lendo, publicando, gerenciando artigos, comentando nos artigos, respondendo aos comentarios uns dos outros e muito mais.

## Como Rodar a Aplicação Corretamente

### Pré requisitos

É necessário ter instalados corretamente as seguintes dependências:

- Servidor de banco de dados MySQL v8.0+;
- Linguagem PHP v8.1+;
- [Composer](https://getcomposer.org/) v2.0+ gerenciador de dependências.

> **Nota:** É opcional o uso de um servidor web, pois a aplicação funciona perfeitamente com o servidor embutido do PHP. Basta executar `php -S localhost:80 -t public` no terminal aberto dentro da pasta raiz do projeto. No entanto, este servidor embutido é limitado e não é ideal para produção. Apenas para desenvolvimento. Utilize um servidor web profissional capaz de trabalhar com PHP como Apache ou Nginx.
> Obtenhar mais informações sobre o servidor embutido do PHP na [página de documentação](https://www.php.net/manual/pt_BR/features.commandline.webserver.php) sobre ele.

1. Você deve renomear o arquivo `.env.exemple` na pasta raíz do projeto para `.env` ou fazer uma cópia dele e renomear 
para `.env`;
2. substituir todos os valores (ou pelo menos nas variáveis de conexão ao banco de dados) das variáveis de ambientes
dentro de `.env` para valores corretos, pois elas estão com valores demonstrativos que não funcionam;
3. executar o arquivo em `data/database/main_database.sql` no seu servidor de banco de dados. Ele criará o banco de dados **brapedia** e todas as 
tabelas nescessárias para o funcionamento do sistema;
4. abrir o arquivo `src/boot/config.php` e lá dentro, definir o valor da constante CONF_BASE_URL para a URL correta em 
localhost conforme o endereço em que a aplicação está sendo executada no servidor. Por exemplo `const CONF_BASE_URL = 'http://localhost:8888'` e também a 
constante URL em `public/assets/scripts/main.js` no início do arquivo logo abaixo dos imports;
__________
config.php
```php
<?php

// BASE URL
const CONF_BASE_URL = 'http://localhost:8888';
```
_________________________
main.js
```javascript
const URL = 'http://localhost:8888';
```
Os exemplos acima assumem que você está executando o servidor em localhost na porta 8888. Caso não for use, a URL específica.
Após isto, iniciar o servidor web dentro da pasta `/public`. Pois é ela que contem toda a saída que será retornada para o cliente.

Estes passos permitem que ao iniciar um servidor web capaz de executar o PHP e acessar a respectiva URL pelo navegador, 
você consiga ver a aplicação executando.

### Contato do Desenvolvedor

- Email: [joseniltonduarte3@gmail.com](mailto:joseniltonduarte3@gmail.com)
- [Linkedin](https://www.linkedin.com/in/nilton-duarte-05b530175/)
- [Instagram](https://www.instagram.com/duarte_2000/)
- [Twitter](https://twitter.com/NiltonD17284468)


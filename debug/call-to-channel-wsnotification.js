
let ch1 = 'article_can&ccedil;&atilde;o_de_68_3';
let ch2 = 'comment_Eu_prefiro_24_3';
let ch3 = 'commentRepply_Não_tô_45_3';

let data = {};
data.username = 'Daniel';
data.msg = 'Blá blá blá';
data.photo = '1663195074-eu-pb.jpg';
data.url = `http://localhost/artigo/9`;
data.comment_id = 'comment-response-67';
data.element_id = 9;

let msg = JSON.stringify(data);
conn.publish(ch1, msg);

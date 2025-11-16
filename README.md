# Sistema de Gerenciamento de Projetos – Engenharia de Software

Aplicação web desenvolvida em **Laravel + Laravel Breeze** para a disciplina de Engenharia de Software.  
O sistema permite:

- Cadastro e autenticação de usuários;
- Criação e edição de projetos;
- Cadastro de membros em cada projeto;
- Visualização de um quadro de tarefas (estilo Kanban) dentro de cada projeto.

---

## 🧰 Requisitos

Para rodar o projeto localmente, você precisa ter instalado:

- **PHP** (>= 8.1)
- **Composer**
- **Node.js** (com **npm**)
- **Git**
- **SQLite** (opcional – o projeto já vem configurado para usar SQLite via arquivo `database.sqlite`)

> Obs.: As versões exatas estão definidas no `composer.json` e `package.json`.

---

## 🔧 Passo a passo para rodar o projeto

### 1. Clonar o repositório

```bash
git clone https://github.com/Tiagonuness/engenhariaSoftware.git
cd engenhariaSoftware

# 🎓 Frequência Certa

![Status](https://img.shields.io/badge/Status-Em_Desenvolvimento-yellow)
![Laravel](https://img.shields.io/badge/Laravel-10+-FF2D20?style=flat&logo=laravel&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=flat&logo=pwa&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)

> **Sistema de Gestão e Controle de Frequência Escolar com foco no programa Pé-de-Meia.**

---

## 📖 Sobre o Projeto

O **Frequência Certa** é uma solução web e mobile (PWA) desenvolvida para ajudar estudantes do ensino médio e técnico a gerenciarem sua assiduidade escolar. 

O objetivo é evitar reprovações por falta e garantir a manutenção de benefícios financeiros governamentais (como o **Pé-de-Meia**) que exigem frequência mínima de 80%.

### 🚀 Principais Funcionalidades
- **Mobile First:** Funciona como App no celular (PWA) e no computador.
- **Grade Dinâmica:** O aluno monta seu horário semanal.
- **Cálculo Automático:** Projeção de faltas permitidas baseada no calendário letivo.
- **Alertas Visuais:** Cores (Verde/Amarelo/Vermelho) indicando risco de reprovação.

---

## 🛠️ Stack Tecnológica

| Front-end & Mobile | Back-end & Dados | Ferramentas |
| :--- | :--- | :--- |
| ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) **HTML5** | ![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white) **Laravel** | ![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white) **Git** |
| ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) **CSS3** | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) **PHP 8** | ![VS Code](https://img.shields.io/badge/VS_Code-007ACC?style=flat-square&logo=visual-studio-code&logoColor=white) **VS Code** |
| ![JS](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) **JavaScript** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) **MySQL** | ![GitHub](https://img.shields.io/badge/GitHub-181717?style=flat-square&logo=github&logoColor=white) **GitHub** |
| ![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=flat-square&logo=bootstrap&logoColor=white) **Bootstrap/Tailwind** | | |

---

## 👥 Equipe de Desenvolvimento

| Integrante | Funções Principais | GitHub |
| :--- | :--- | :--- |
| **Nicolas Viana Alves** | Full-Stack, Documentação | [@naicolas-br](https://github.com/naicolas-br) |
| **Bruno Felix Seixas** | Front-end, Design | [@obrunofelix](https://github.com/obrunofelix) |
| **Igor Thiago Costa Rodrigues** | Back-end | [@luxxzvh](https://github.com/luxxzvh) |

---

## ✅ Checklist de Desenvolvimento

*OBS PARA OS INTEGRANTES: Marque as caixas `[x]` editando este arquivo no GitHub conforme o progresso.*

### 🏗️ Fase 1: Configuração & Back-end (02/12 - 10/12)
- [x] Criar repositório e configurar Git.
- [x] Instalar Laravel e configurar ambiente (`.env`).
- [x] **Banco de Dados:** Criar Migrations (Users, Disciplinas, Frequencias).
- [x] **Banco de Dados:** Criar Models e Relationships.
- [x] **API:** Criar Controllers básicos (CRUD Disciplinas).
- [x] Implementar Autenticação (Laravel Breeze/Sanctum).

### 🎨 Fase 2: Front-end & Interface (11/12 - 20/12)
- [x] Definir Paleta de Cores e Identidade Visual.
- [x] **Tela Login/Cadastro:** Criar layout responsivo.
- [x] **Tela Grade (Web):** Criar tabela de horários desktop.
- [x] **Tela Grade (Mobile):** Criar sistema de abas por dia da semana.
- [x] **Componentes:** Criar Cards de matéria com indicadores de cor.

### 🧠 Fase 3: Lógica & Integração (21/12 - 05/01)
- [x] **Lógica:** Algoritmo de cálculo de % de frequência.
- [ ] **Lógica:** Sistema de projeção de dias letivos.
- [x] Conectar Front-end com a API (Axios/Fetch).
- [ ] Implementar funcionalidade "Marcar Feriado/Sem Aula".
- [x] Implementar funcionalidade "Registrar Presença".

### 📱 Fase 4: PWA & Finalização (06/01 - 01/02)
- [ ] Configurar `manifest.json` (Ícones, Nome, Cores).
- [ ] Configurar Service Workers (Cache offline).
- [ ] Testar instalação no Android/iOS.
- [ ] Correção de Bugs e refinamento visual.
- [ ] Escrever Documentação Final.

---

## 🚀 Como rodar o projeto localmente

1. Clone o repositório:
```bash
git clone <URL_DO_REPOSITORIO>
cd <NOME_DO_PROJETO>
```
2. Instale as dependências do PHP:
```Bash
composer install
```
3. Instale as dependências do Front-end:
```bash
npm install
```
4. Configure o arquivo .env:
```bash
cp .env.example .env
php artisan key:generate
```
5. Configure o banco de dados no .env e rode as migrations:
```bash
php artisan migrate
```
6. Inicie o servidor
```bash
php artisan serve
npm run dev
```
## 📄 Licença
Este projeto está sob a licença MIT. Consulte o arquivo LICENSE para mais detalhes.

Desenvolvido para o TCC do Curso Técnico em Desenvolvimento de Sistemas - 2025/2026.

# Really: A PHP Microblog Platform

Really is a lightweight, flat-file PHP microblog platform designed for simplicity and flexibility. Built with modern PHP practices, Really offers a custom CMS for markdown-based content, seamless Docker integration for easy deployment, and extends functionality with TailwindCSS for a stylish and responsive design.

## Features

- **Flat-File CMS**: Store your pages and posts in markdown files for ease of use and version control friendliness.
- **Docker Support**: Containerized setup with Docker for consistent development and deployment environments.
- **Custom Routing**: Leverage a custom-built routing system to handle requests efficiently, supporting dynamic URLs and route groups.
- **Markdown Rendering**: Utilize Parsedown for parsing markdown content, enhanced with TailwindCSS Typography for beautiful prose.
- **No Database Required**: Eliminates the complexity of database management, with all content stored in flat markdown files.
- **Admin Panel**: Manage your content easily through a simple yet powerful admin dashboard.
- **Security**: Emphasize security with environment variables for configuration, and secure handling of admin credentials.
- **Extensibility**: Designed for easy customization and extension, allowing developers to add new features and integrations.

## Installation

1. **Clone the repository**:

   ```bash
   git clone https://github.com/yourusername/really.git
   cd really
   ```

2. **Build the Docker container**:

   ```bash
   docker-compose up --build
   ```

3. **Access the platform**:
   Navigate to `http://localhost:3000` in your web browser to access the Really platform.

## Usage

- **Creating Content**: Add markdown files to the `content/pages` and `content/posts` directories to create static pages and blog posts.
- **Admin Panel**: Access the admin panel by navigating to `/admin`. Default login credentials can be configured in the `.env` file.
- **Customizing Styles**: Modify the TailwindCSS configuration and styles in the `src/css` directory and rebuild using Bun.

## Development

To contribute to Really or customize it for your needs:

1. **Install Node.js dependencies**:

   ```bash
   bun install
   ```

2. **Run TailwindCSS build**:

   ```bash
   bun run tailwind:build
   ```

3. **Customize Routing and Controllers**: Extend the `Router` and `Controller` classes in the `src` directory to add new functionality or modify existing features.

## Contributing

Contributions to Really are welcome! Whether it's bug fixes, new features, or improvements to the documentation, your help is appreciated. Please submit a pull request or open an issue to contribute.

## License

Really is open-source software licensed under the MIT license.

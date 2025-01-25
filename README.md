```markdown
# Codebot Laravel Package

Codebot is a Laravel package that uses **DeepSeek AI** to generate **migrations**, **models**, and **views** with designs using popular frameworks like **Bootstrap** and **Tailwind CSS**. It simplifies the process of scaffolding Laravel applications by automating repetitive tasks.


## Features

- Generate **migrations** with fields and relationships.
- Generate **models** with fillable attributes and relationships.
- Generate **views** with **Bootstrap** or **Tailwind CSS** designs.
- Interactive command-line interface for easy usage.
- Customizable templates for migrations, models, and views.

---

## Installation

1. Install the package via Composer:
   ```bash
   composer require hussainabuhajjaj/codebot-laravel
   ```

2. Publish the configuration file (optional):
   ```bash
   php artisan vendor:publish --provider="Hussainabuhajjaj\Codebot\CodebotServiceProvider" --tag="config"
   ```

3. Set your DeepSeek API key in the `.env` file:
   ```env
   DEEPSEEK_API_KEY=your-api-key-here
   ```

---

## Usage

Run the following command to generate code interactively:
```bash
php artisan codebot:generate
```

### Example Workflow

1. **Generate a Migration:**
   - Enter the table name (e.g., `users`).
   - Add fields (e.g., `name:string`, `email:string`, `password:string`).
   - Add relationships (e.g., `hasMany:posts`).

2. **Generate a Model:**
   - The model will be created with fillable attributes and relationships.

3. **Generate Views:**
   - Choose a design framework (e.g., **Bootstrap** or **Tailwind CSS**).
   - Views will be generated for listing, creating, and editing records.

---

## Examples

### Example 1: Generate a `users` Table

1. Run the command:
   ```bash
   php artisan codebot:generate
   ```

2. Follow the prompts:
   - Table name: `users`
   - Fields:
     - `name:string`
     - `email:string`
     - `password:string`
   - Relationships: `hasMany:posts`
   - Design framework: `bootstrap`

3. **Results:**
   - A migration file is created:
     ```php
     Schema::create('users', function (Blueprint $table) {
         $table->id();
         $table->string('name');
         $table->string('email')->unique();
         $table->string('password');
         $table->timestamps();
     });
     ```
   - A model file is created:
     ```php
     class User extends Model
     {
         protected $fillable = ['name', 'email', 'password'];

         public function posts()
         {
             return $this->hasMany(Post::class);
         }
     }
     ```
   - Views are created with Bootstrap styling:
     - `resources/views/users/index.blade.php`
     - `resources/views/users/create.blade.php`
     - `resources/views/users/edit.blade.php`

---

### Example 2: Generate a `posts` Table

1. Run the command:
   ```bash
   php artisan codebot:generate
   ```

2. Follow the prompts:
   - Table name: `posts`
   - Fields:
     - `title:string`
     - `content:text`
     - `user_id:integer`
   - Relationships: `belongsTo:user`
   - Design framework: `tailwind`

3. **Results:**
   - A migration file is created:
     ```php
     Schema::create('posts', function (Blueprint $table) {
         $table->id();
         $table->string('title');
         $table->text('content');
         $table->foreignId('user_id')->constrained();
         $table->timestamps();
     });
     ```
   - A model file is created:
     ```php
     class Post extends Model
     {
         protected $fillable = ['title', 'content', 'user_id'];

         public function user()
         {
             return $this->belongsTo(User::class);
         }
     }
     ```
   - Views are created with Tailwind CSS styling:
     - `resources/views/posts/index.blade.php`
     - `resources/views/posts/create.blade.php`
     - `resources/views/posts/edit.blade.php`

---

## Results

After running the `codebot:generate` command, you’ll have the following files:

1. **Migrations:**
   - Located in `database/migrations`.
   - Ready to be applied using `php artisan migrate`.

2. **Models:**
   - Located in `app/Models`.
   - Includes fillable attributes and relationships.

3. **Views:**
   - Located in `resources/views`.
   - Styled with **Bootstrap** or **Tailwind CSS**.

4. **Routes:**
   - Add routes in `routes/web.php` to access the generated views:
     ```php
     Route::resource('users', UserController::class);
     Route::resource('posts', PostController::class);
     ```

---

## Contributing

Contributions are welcome! Here’s how you can contribute:

1. Fork the repository.
2. Create a new branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. Commit your changes:
   ```bash
   git commit -m "Add your feature"
   ```
4. Push to the branch:
   ```bash
   git push origin feature/your-feature-name
   ```
5. Open a pull request.

---

## License

This package is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT).

---

## Support

If you encounter any issues or have questions, please open an issue on [GitHub](https://github.com/hussainabuhajjaj/Codebot-laravel/issues).

---

Enjoy using **Codebot**! 🚀
```

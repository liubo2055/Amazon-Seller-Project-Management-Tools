<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStatisticsView extends Migration{
  public function up():void{
    DB::statement('DROP VIEW IF EXISTS statistics');

    $sql=<<<END
CREATE VIEW statistics AS (
  SELECT
    projects.*,
    code,
    product_asin,
    product_url,
    marketplace,
    seller_id,
    seller_name,
    storefront_url,
    storefront_name,
    keywords,
    users.name AS freelancer
  FROM projects
  JOIN todos
  ON todo_id=todos.id
  LEFT JOIN users
  ON projects.user_id=users.id
)

END;

    DB::statement($sql);
  }

  public function down():void{
    DB::statement('DROP VIEW statistics');

    $sql=<<<END
CREATE VIEW statistics AS (
  SELECT
    projects.*,
    code,
    product_asin,
    product_url,
    marketplace,
    seller_id,
    seller_name,
    storefront_url,
    users.name AS freelancer
  FROM projects
  JOIN todos
  ON todo_id=todos.id
  LEFT JOIN users
  ON projects.user_id=users.id
)

END;

    DB::statement($sql);
  }
}

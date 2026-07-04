<?php
/**
 * Role Model
 */

require_once dirname(__DIR__, 2) . '/core/Model.php';

class Role extends Model {
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
}

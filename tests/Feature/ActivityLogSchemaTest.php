<?php

use Illuminate\Support\Facades\Schema;

it('includes attribute_changes on activity_log for spatie activity log', function () {
    expect(Schema::hasTable('activity_log'))->toBeTrue();
    expect(Schema::hasColumn('activity_log', 'attribute_changes'))->toBeTrue();
});

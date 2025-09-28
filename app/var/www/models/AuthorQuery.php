<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Author]].
 *
 * @see Author
 */
class AuthorQuery extends \yii\db\ActiveQuery
{
    /**
     * @param $year
     * @return AuthorQuery
     */
    public function topByYear($year = null): AuthorQuery
    {
        $query = $this->select([
                '{{%authors}}.*',
                'books_count' => 'COUNT(DISTINCT {{%books}}.id)'
            ])
            ->innerJoinWith('bookAuthorsRelation.bookRelation')
            ->groupBy('{{%authors}}.id');

        if ($year !== null) {
            $query->andWhere(['{{%books}}.year' => $year]);
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     * @return Author[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Author|array|null
     */
    public function one($db = null): Author|array|null
    {
        return parent::one($db);
    }
}

<?php

/**
 * 
 */
class Utils
{
    /**
     * Will return a random immutable
     * @Return \DateTimeImmutable
     */
    public static  function randomDate()
    {
        $randomDate = new DateTimeImmutable(
            date(
                "Y-m-d",
                mt_rand(1, time())
            )
        );

        return $randomDate;
    }
}

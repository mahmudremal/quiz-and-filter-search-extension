<?php
/**
 * Custom template tags for the theme.
 *
 * @package QuizAndFilterSearch
 */
if( ! function_exists( 'is_FwpActive' ) ) {
  function is_FwpActive( $opt ) {
    if( ! defined( 'QUIZ_AND_FILTER_SEARCH_OPTIONS' ) ) {return false;}
    return ( isset( QUIZ_AND_FILTER_SEARCH_OPTIONS[ $opt ] ) && QUIZ_AND_FILTER_SEARCH_OPTIONS[ $opt ] == 'on' );
  }
}
if( ! function_exists( 'get_FwpOption' ) ) {
  function get_FwpOption( $opt, $def = false ) {
    if( ! defined( 'QUIZ_AND_FILTER_SEARCH_OPTIONS' ) ) {return false;}
    return isset( QUIZ_AND_FILTER_SEARCH_OPTIONS[ $opt ] ) ? QUIZ_AND_FILTER_SEARCH_OPTIONS[ $opt ] : $def;
  }
}
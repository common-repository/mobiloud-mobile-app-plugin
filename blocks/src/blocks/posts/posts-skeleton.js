import React from 'react';
import ContentLoader from 'react-content-loader';

const PostItem = ( { yOffset } ) => (
	<>
		<rect x="0px" y={ `${ 0 + yOffset }px` } width="128px" height="128px" />
		<rect x="144px" y={ `${ 0 + yOffset }px` } width="250px" height="19px" />
		<rect x="144px" y={ `${ 27 + yOffset }px` } width="150px" height="15px" />
		<rect x="144px" y={ `${ 55 + yOffset }px` } width="200px" height="15px" />
		<rect x="144px" y={ `${ 75 + yOffset }px` } width="250px" height="15px" />

		<rect x="144px" y={ `${ 125 + yOffset }px` } width="50px" height="15px" />
		<rect x="204px" y={ `${ 125 + yOffset }px` } width="150px" height="15px" />
		<rect x="144px" y={ `${ 145 + yOffset }px` } width="75px" height="15px" />
		<rect x="229px" y={ `${ 145 + yOffset }px` } width="135px" height="15px" />
	</>
)

export const PostsSkeleton = ( props ) => {
  return ( <ContentLoader viewBox="0 0 500px 570px" height={ 1050 } width={ 480 } {...props}>
		<PostItem yOffset={ 0 } />
		<PostItem yOffset={ 190 } />
		<PostItem yOffset={ 380 } />
		<PostItem yOffset={ 570 } />
		<PostItem yOffset={ 760 } />
  </ContentLoader> )
}

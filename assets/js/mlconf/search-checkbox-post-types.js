import { useState, useEffect } from "react";
import ReactDOM from "react-dom";

const SearchCheckboxPostTypes = () => {
	const $ = jQuery;
	const [ postTypes, setPostTypes ] = useState( false );
	const [ searchResults, setSearchResults ] = useState( [] );
	const [ allSelected, setAllSelected ] = useState( false );
	let defaultSelectAll = true;

	useEffect( () => {
		$.ajax( {
			url: ajaxurl,
			data: {
				action: 'mlconf_get_post_types'
			}
		} ).done( response => {
			if ( response.success ) {
				setPostTypes( response.data );
				setSearchResults( response.data );
				response.data.map( ( item ) => {
					if ( item.selected ) {
						defaultSelectAll = true;
					} else {
						defaultSelectAll = false;
					}

					return item;
				} );

				setAllSelected( defaultSelectAll );
			}
		} );
	}, [] );

	const searchPostTypes = ( e ) => {
		if ( ! e.target.value ) {
			setSearchResults( postTypes.map( item => {
				item.display = true;
				return item;
			} ) );

			return;
		}

		setSearchResults( postTypes.map( item => {
			if ( ! item.postType.toLowerCase().includes( e.target.value.toLowerCase() ) ) {
				item.display = false;
			} else {
				item.display = true;
			}

			return item;
		} ) );
	};

	const selectAll = () => {
		setSearchResults( searchResults.map( ( item ) => {
			if ( allSelected ) {
				item.selected = false;
			} else {
				item.selected = true;
			}
			return item;
		} ) );

		setAllSelected( ! allSelected );
	};

	const selectPostType = ( e ) => {
		const modPostTypes = postTypes.map( item => {
			if ( item.postType === e.target.value ) {
				if ( e.target.checked ) {
					item.selected = true;
				} else {
					item.selected = false;
				}
			}

			return item;
		} );

		setSearchResults( modPostTypes );
	};

	if ( ! postTypes ) return null;

	return (
		<div className='ml-col-half mlconf__settings__search-checkbox-list'>
			<p>Select which post types should be included in the article list.</p>
			<input
				type="search"
				className="mlconf__settings__search-checkbox-list--search mlconf__settings__search-checkbox-list--search-category"
				placeholder="Search post type..."
				onChange={ searchPostTypes }
			/>

			<div className="mlconf__settings__search-checkbox-list-list">
				{
					searchResults.map( ( item, index ) => {
						let display = 'block';

						if ( undefined === item?.display ) {
							display = 'block';
						} else {
							if ( item.display ) {
								display = 'block';
							} else {
								display = 'none';
							}
						}

						return (
							<div key={ index } style={ { display: display } }>
								<input type="checkbox" id={ `postypes_${ item.postType }` } name="postypes[]" value={ item.postType } checked={ item.selected ? 'checked' : '' } onChange={ selectPostType } />
								<label htmlFor={ `postypes_${ item.postType }` }>{ item.postType }</label>
							</div>
						)
					} )
				}
			</div>
			<div className="mlconf__settings__search-checkbox__button-controls">
				<button type="button" className="button" onClick={ selectAll }>{ allSelected ? 'Deselect All' : 'Select All' }</button>
			</div>
		</div>
	);
};

const root = document.getElementById( 'mlconf__settings__search-checkbox-list-post-types' );

if ( root ) {
	ReactDOM.render(
		<SearchCheckboxPostTypes />,
		root
	);
}

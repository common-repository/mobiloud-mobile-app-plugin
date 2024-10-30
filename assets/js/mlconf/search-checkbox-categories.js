import { useState, useEffect } from "react";
import ReactDOM from "react-dom";

const SearchCheckboxCategories = () => {
	const $ = jQuery;
	const [ categories, setCategories ] = useState( false );
	const [ searchResults, setSearchResults ] = useState( [] );
	const [ allSelected, setAllSelected ] = useState( false );
	let defaultSelectAll = true;

	useEffect( () => {
		$.ajax( {
			url: ajaxurl,
			data: {
				action: 'mlconf_get_categories'
			}
		} ).done( response => {
			if ( response.success ) {
				setCategories( response.data );
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

	const searchCategories = ( e ) => {
		if ( ! e.target.value ) {
			setSearchResults( categories.map( item => {
				item.display = true;
				return item;
			} ) );

			return;
		}

		setSearchResults( categories.map( item => {
			if ( ! item.category.toLowerCase().includes( e.target.value.toLowerCase() ) ) {
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

	const selectCategory = ( e ) => {
		const modcategories = categories.map( item => {
			if ( item.category === e.target.value ) {
				if ( e.target.checked ) {
					item.selected = true;
				} else {
					item.selected = false;
				}
			}

			return item;
		} );

		setSearchResults( modcategories );
	};

	if ( ! categories ) return null;

	return (
		<div className='ml-col-half mlconf__settings__search-checkbox-list'>
			<p>Select which categories should be included in the article list.</p>
			<input
				type="search"
				className="mlconf__settings__search-checkbox-list--search mlconf__settings__search-checkbox-list--search-category"
				placeholder="Search category..."
				onChange={ searchCategories }
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
								<input type="checkbox" id={ `categories_${ item.slug }` } name="categories[]" value={ item.category } checked={ item.selected ? 'checked' : '' } onChange={ selectCategory } />
								<label htmlFor={ `categories_${ item.slug }` }>{ item.category }</label>
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

const root = document.getElementById( 'mlconf__settings__search-checkbox-list-categories' );

if ( root ) {
	ReactDOM.render(
		<SearchCheckboxCategories />,
		root
	);
}

//import { useState } from "react";
//import Axios from "axios";

console.log("Salut");

class LikeButton extends React.Component {
  constructor(props){
    super(props);

    this.state = {
      likes: props.likes || 0,
      isLiked: props.isLiked || false
    };

    //this.handleClick = this.handleClick.bind(this);
  }

  handleClick(){
    const isLiked = this.state.isLiked;
    const likes = this.state.likes + (isLiked ? -1 : 1);
    this.setState({ likes: likes, isLiked: !isLiked });
    //manque la requete AJAX ici qui va prevenir le serveur
    //Axios.post('http://localhost/sorties/public/sortie/like', {likes: likes, isLiked : isLiked})
  }

  render(){
    return React.createElement(
      'button', 
      //{ onChange={event} => setLike(event.target.value) },
      { className: 'btn btn-link', onClick: () => this.handleClick() }, 
      this.state.likes,
      " ",
      React.createElement('i', {className: this.state.isLiked ? "fas fa-thumbs-up" : "far fa-thumbs-up"}),
      " ",
      this.state.isLiked ? "J'aime" : "Aimer "
      );
  }
}

document.querySelectorAll('span.react-like').forEach(function(span){
  ReactDOM.render(React.createElement(LikeButton), span);
});